<?php

namespace controllers;

use models\HttpConflictException;
use models\HttpNotFoundException;
use Nowakowskir\JWT\JWT;
use Nowakowskir\JWT\TokenDecoded;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use models\Access;
use models\User;
use utils\MailSender;

class UserController extends Controller
{
    private User $User;
    private array $config;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->User = $this->DIcontainer->get(User::class);
        $this->config = $this->DIcontainer->get('settings')['UserController'];
    }

    private function generateToken(): string
    {
        //creating new token
        $tokenDecoded = new TokenDecoded(
            ['typ' => 'JWT', 'alg' => JWT::ALGORITHM_HS512],
            [
                'user_id' => $this->User->data['user_id'],
                'access_id' => $this->User->data['access_id'],
                'email' => $this->User->data['email'],
                'assigned' => time(),
                'ip' => getHostByName(getHostName())
            ]
        );
        // encoding the token
        $tokenEncoded = $tokenDecoded->encode($this->config['jwt']['signature'], JWT::ALGORITHM_HS512);
        return $tokenEncoded->__toString();
    }

    private function getRandomKey(): string
    {
        return base64_encode(random_bytes($this->config['activation_key_len']));
    }

    private function initiateEmailChange(string $email): void
    {
        /**
         * Sending email to user with code to change email, and setting action_key
         * 
         * @param string $email new email
         */
        if ($this->User->exist(['email'])) {
            throw new HttpConflictException('Given mail ' . $this->User->data['email'] . ' already exist.');
        }

        // mail will be send on new email
        $this->User->data['email'] = $email;

        // this code will be delivered to user
        $this->User->data['action_key'] = $this->getRandomKey(6);

        $this->User->fieldUpdatePolicy('email', $this->User->data);

        $MailSender = $this->DIcontainer->get(MailSender::class);
        $MailSender->setUser($this->User->data);
        $MailSender->setMailSubject('Change email');
        $MailSender->send();

        // action key is for ex.: szymon1256@somemail.com::skufhi
        $this->User->update(['action_key' => $email . '::' . $this->User->data['action_key']]);
    }

    public function finishEmailChange(string $userKey): void
    {
        /**
         * Change email when $userKey is correct
         */
        [$newEmail, $key] = explode('::', $this->User->data['action_key']);

        if ($key !== $userKey) {
            throw new \models\HttpBadRequestException('Given key `' . $userKey . '` is not valid');
        }

        $this->User->data['email'] = $newEmail;
        $this->User->update(['action_key' => Null, 'email' => $newEmail], $this->User->data['id']);
    }

    // POST /auth
    public function authUser(Request $request, Response $response, $args): Response
    {
        /**
         * Verifying User by email and password
         * POST /auth
         * {
         *    email:{"type":"string"},
         *    password:{"type":"string"}
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        list(
            'email' => $email,
            'password' => $password
        ) = $this->getParsedData($request);

        if (!isset($email, $password)) {
            throw new HttpBadRequestException($request, 'Fields `email` and `password` are required');
        }

        try {
            $this->User->login($email, $password);

            //check is user activated
            if ($this->User->data['activated'] === false) {
                throw new HttpConflictException("Can not authenticate because user is not activated");
            };
        } catch (HttpNotFoundException $e) {
            // is user not found, User->id is not set so can't log activity
            throw $e;
        } catch (\Throwable $e) {

            $this->Log->create([
                'user_id' => $this->User->data['id'],
                'message' => 'USER ' . $email . ' NOT VERIFIED DATA ' . $e->getMessage()
            ]);
            throw $e;
        }

        $Access = $this->DIcontainer->get(Access::class);
        $data = [
            'jwt' => $this->generateToken(),
            'userID' => $this->User->data['id'],
            'access' => $Access->read(['id' => $this->User->data['access_id']])
        ];

        $this->Log->create([
            'user_id' => $this->User->data['id'],
            'message' => 'USER ' . $email . ' VERIFIED'
        ]);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // POST /users
    public function registerNewUser(Request $request, Response $response, $args): Response
    {
        /**
         * Creating new User
         * POST /users
         * {
         *    "name":{"type":"string"},
         *    "surname":{"type":"string"},
         *    "email":{"type":"string"},
         *    "password":{}
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */

        $data = $this->getParsedData($request);

        $data['access_id'] = $this->DIcontainer->get('settings')['default_params']['access'];
        $data['action_key'] = $this->getRandomKey(6);

        $data['id'] = $this->User->register($data);
        unset($data['password']);

        $this->Log->create(array(
            'user_id' => $data['id'],
            'message' => "USER " . $data['email'] . " CREATE user DATA " . json_encode($data)
        ));

        $MailSender = $this->DIcontainer->get(MailSender::class);
        $MailSender->setUser($data);
        $MailSender->setMailSubject('User Activation');
        $MailSender->send();

        return $response->withStatus(201, "Created");
    }

    // POST /users/actions/{action}
    public function userAction(Request $request, Response $response, $args): Response
    {
        /**
         * Activating user and redirect user to given url
         * action: "resend" | "activate | change_email"
         * POST /users/action
         * {
         *      "password" : "",
         *      "email" : "",
         *      "key" : "",
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */

        $data = $this->getParsedData($request);

        if (!isset($data['password'], $data['email'], $data['key'])) {
            throw new HttpBadRequestException($request, 'Fileds `password`, `email`and `key` are required');
        }

        $this->User->login((string)$data['email'], (string)$data['password']);
        $this->Log->create([
            'message' => 'USER ' . $data['email'] . ' VERIFIED IN actions DATA ' . json_encode(['action' => $args['action']]),
            'user_id' => $this->User->data['id']
        ]);

        switch ($args['action']) {
            case 'activation':

                $this->User->activate((string)$data['key']);
                $this->Log->create([
                    'user_id' => $this->User->data['id'],
                    'message' => 'USER ' . $data['email'] . ' ACTIVATED DATA ' . json_encode(['activated' => true])
                ]);
                break;

            case 'new_key':
                $this->User->setActionKey($this->getRandomKey(6));

                $MailSender = $this->DIcontainer->get(MailSender::class);
                $MailSender->setUser($this->User->data);
                $MailSender->setMailSubject('User Activation');
                $MailSender->send();

                $this->Log->create([
                    'message' => 'USER ' . $data['email'] . ' UPDATE action_key DATA ' . json_encode(['action_key' => $this->User->data['action_key']]),
                    'user_id' => $this->User->data['id']
                ]);
                break;

            case 'email':
                // actually change email
                $this->finishEmailChange($data['key']);
                //started in UserController::updateUser()

                $this->Log->create([
                    'message' => 'USER ' . $data['email'] . ' UPDATE email DATA ' . json_encode(array_merge($data, ['new_email' => $this->User->data['email']])),
                    'user_id' => $this->User->data['id']
                ]);
                break;

            default:
                $this->Log->create([
                    'message' => 'USER ' . $data['email'] . ' PERFORMED BAD ACTION DATA' . $args['action'],
                    'user_id' => $this->User->data['id']
                ]);
                throw new HttpBadRequestException($request, 'Requested action `' . $args['action'] . '` is not allowed. Allowed actions: `activation`, `new_key`, `email`');
                break;
        }

        $response->getBody()->write('Action `' . $args['action'] . '` succeded.');
        return $response->withStatus(200);
    }

    // GET /users?ext=<acces_id>
    // GET /users/{user_id}?ext=<access_id>
    public function getUsers(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all users
         * returning array of items
         * GET /users?ext=<acces_id>
         * GET /users/{user_id}?ext=<access_id>
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        ['params' => $params, 'mode' => $mode] = $this->getSearchParams($request);
        if (isset($params) && isset($mode))  $this->User->setSearch($mode, $params);

        $this->User->setQueryStringParams($this->parsedQueryString($request));

        $this->switchKey($args, 'userID', 'id');
        $data = $this->handleExtensions($this->User->read($args), $request);

        foreach ($data as &$user) unset($user['password'], $user['action_key']);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // PATCH /users/{user_id}
    public function updateUser(Request $request, Response $response, $args): Response
    {
        /**
         * Updating user informations by user_id
         * PATCH /users/{user_id}
         * {
         *    "name":{string},
         *    "surname":{string},
         *    "email":{string}
         *    "old_password":{string},
         *    "new_password":{string},
         *    "access_id":{integer}
         * } 
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $this->User->data = $this->User->read(['id' => $args['userID']])[0];

        $data = $this->getParsedData($request);

        $currentUser = (int) $request->getAttribute('user_id');
        $accessID = $request->getAttribute('access_id');
        $userEmail = $request->getAttribute('email');

        // checking if user can change acces
        if (isset($data['access_id'])) {
            $Access = $this->DIcontainer->get(Access::class);
            if (
                (bool)$Access->read(['id' => $accessID])[0]['access_edit'] === false
            ) throw new HttpUnauthorizedException($request, 'You do not have acces to edit user access_id');
        }


        // Changing Password
        if (isset($data['old_password'], $data['new_password'])) {

            $this->User->changePassword($data['old_password'], $data['new_password']);

            $this->Log->create([
                'user_id' => $currentUser,
                'message' => 'USER ' . $userEmail . ' UPDATE password DATA '
            ]);

            unset($data['old_password'], $data['new_password']);
        }

        // Changing email
        if (isset($data['email'])) {
            $this->initiateEmailChange($data['email']);

            $this->Log->create([
                'user_id' => $currentUser,
                'message' => 'USER ' . $userEmail . ' WANT TO UPDATE email DATA ' . json_encode($data)
            ]);
            unset($data['email']);
        }

        if (!empty($data)) {
            $this->User->update($data, $args['user_id']);
            $this->Log->create([
                'user_id' => $currentUser,
                'message' => 'USER ' . $userEmail . ' UPDATE USER ' . $this->User->data['email'] . ' DATA ' . json_encode($data)
            ]);
        }

        return $response->withStatus(204, 'Updated');
    }

    // DELETE /users/{user_id}
    public function deleteUser(Request $request, Response $response, $args): Response
    {
        /**
         * deleting user by user_id
         * DELETE /users/{user_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $this->User->data = $this->User->read(['id' => $args['userID']])[0];
        unset($this->User->data['password']);

        $this->User->delete((int) $args['userID']);
        $this->Log->create([
            'user_id' => $args['userID'],
            "message" => 'USER ' . $request->getAttribute('email') . ' DELETE user DATA ' . json_encode($this->User->data)
        ]);

        return $response->withStatus(204, "Deleted");
    }
}
