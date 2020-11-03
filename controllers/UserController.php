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
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use models\Access;
use models\User;
use utils\MailSender;

class UserController extends Controller
{
    private User $User;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->User = $this->DIcontainer->get(User::class);
    }

    private function generateToken(int $userID, int $accessID, string $email): string
    {
        //creating new token
        $tokenDecoded = new TokenDecoded(
            ['typ' => 'JWT', 'alg' => JWT::ALGORITHM_HS512],
            [
                'user_id' => $userID,
                'access_id' => $accessID,
                'email' => $email,
                'assigned' => time(),
                'ip' => getHostByName(getHostName())
            ]
        );
        // encoding the token
        $tokenEncoded = $tokenDecoded->encode($this->DIcontainer->get('settings')['jwt']['signature'], JWT::ALGORITHM_HS512);
        return $tokenEncoded->__toString();
    }

    private function getRandomKey(int $len): string
    {
        return base64_encode(random_bytes($len));
    }

    // POST /auth
    public function verifyUser(Request $request, Response $response, $args): Response
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

        if(!isset($email,$password)) throw new HttpBadRequestException($request,'Fields `email` and `passowrd` are required');
        
        $this->User->setEmail($email);

        try{
            $this->User->login($password);
        }catch(\models\HttpBadRequestException $e){
            
            throw $e;
        }catch(\Throwable $e){
            $this->Log->create(array(
                'user_id' => $this->User->getID(),
                'message' => 'USER ' . $email . ' NOT VERYFIED DATA '.$e->getMessage()
            ));
            throw $e;
        }
        
        $Access = $this->DIcontainer->get(Access::class);
        $data = [
            'jwt' => $this->generateToken($this->User->getID(), $this->User->data['access_id'], $email),
            'userID' => $this->User->getID(),
            'access' => $Access->read(['id' => $this->User->data['access_id']])
        ];
        $this->User->update(array('login_fails' => 0));
        $this->Log->create(array(
            'user_id' => $this->User->getID(),
            'message' => 'USER ' . $email . ' VERIFIED'
        ));

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
         *    "repeat_password":{}
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        // list(
            // 'name' => $name,
            // 'surname' => $surname,
            // 'email' => $email,
            // 'password' => $password,
            // 'repeat_password' => $repeat_password
        // )
        $data = $this->getParsedData($request);

        if ($data['password'] !== $data['repeat_password']) throw new HttpBadRequestException($request, 'Given fields `password` and `repeat_password` are required to have the same value');
        unset($data['repeat_password']);

        $data['access_id'] = $this->DIcontainer->get('settings')['default_params']['access'];
        $data['action_key'] = $this->getRandomKey(6);

        $this->User->register($data);

        $data['id'] = $this->User->create($data);
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

    // POST /users/action
    public function userAction(Request $request, Response $response, $args): Response
    {
        /**
         * Activating user and redirect user to given url
         * POST /users/action
         * {
         *      "password" : "",
         *      "email" : "",
         *      "key" : "",
         *      "action" : "resend" | "activate | change_email"
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        [
            'password' => $password,
            'email' => $email,
            'key' => $key,
            'action' => $action
        ] = $this->getParsedData($request);
        
        if(
            !isset($password,$email,$key,$action)
        ) throw new HttpBadRequestException($request,'Fileds `password`, `email`, `key` and `action` are required');

        if($key === 'NONE_NONE')throw new HttpBadRequestException($request,'Given `key` is not valid');

        if (
            !in_array($action, ['activate', 'resend', 'change_email'])
        ) throw new HttpBadRequestException($request, 'You have to specified action to `activate`, `resend` or `change_email`');

        if ($action === 'change_email') {
            $user = $this->User->read(['action_key' => $key])[0];
            if ((bool)$user['activated'] === false) {
                throw new HttpException($request, "Given user is not activated!. You ave to activate account to change email", 409);
            }
        } else {
            $user = $this->User->read(['action_key' => $key, 'email' => $email])[0];
            if ((bool)$user['activated'] === true) {
                throw new HttpException($request, "Given user is already activated!", 409);
            }
        }

        if (!password_verify($password, $user['password'])) {
            throw new HttpBadRequestException($request, "Given password is not correct");
        }

        $this->User->setID($user['id']);
        switch ($action) {
            //send activation mail again
            case 'resend':
                $user['action_key'] = $this->getRandomKey(6);
                $this->User->update(['action_key' => $user['action_key']]);

                $MailSender = $this->DIcontainer->get(MailSender::class);
                $MailSender->setUser($user);
                $MailSender->setMailSubject('Resend User Activation');
                $MailSender->send();

                $this->Log->create([
                    'user_id' => $user['id'],
                    'message' => 'USER ' . $user['email'] . ' RESEND ACTIVATION DATA '
                ]);

                $response->getBody()->write(json_encode('Your Code has been resended'));
                break;

            case 'activate':
                if ($user['action_key'] !== $key) {
                    throw new HttpBadRequestException($request, 'Your activation key is not correct');
                }
                $this->User->setID($user['id']);
                $this->User->update(['activated' => 1, 'action_key' => 'NONE_NONE']);
                $this->Log->create([
                    'user_id' => $user['id'],
                    'message' => 'USER ' . $user['email'] . ' ACTIVATED DATA ' . json_encode(['activated' => true])
                ]);

                $response->getBody()->write(json_encode('User succesfully activated'));
                break;

            case 'change_email':
                /* given email is new user email - it's not set yet*/
                $editedEmail = ['email' => $email];
                if ($this->User->exist($editedEmail)) {
                    throw new HttpConflictException('Given email ' . $email . ' already exist. Someone activated the same email before You.');
                }
                $editedEmail['action_key'] = 'NONE_NONE';
                // setting new email
                $this->User->update($editedEmail);
                $this->Log->create([
                    'user_id' => $user['id'],
                    'message' => 'USER ' . $user['email'] . ' UPDATE user DATA ' . json_encode($editedEmail)
                ]);
                $response->getBody()->write(json_encode('Email changed to ' . $email));
                break;
            default:
                break;
        }
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
    public function updateUserInformations(Request $request, Response $response, $args): Response
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

        $editedUser = $this->User->read(['id' => $args['userID']])[0];
        $this->User->setID($editedUser['id']);
        
        // Changing Password
        if (isset($data['old_password'], $data['new_password'])) {
            if ($data['old_password'] === $data['new_password']) {
                throw new HttpBadRequestException($request, 'Incorrect passowrds values - old_password and new_password can not be the same');
            }
            if (password_verify($data['old_password'], $editedUser['password'])) {
                $data['password'] = password_hash($data['new_password'], PASSWORD_BCRYPT, ['cost' => 12]);
            }
            unset($data['new_password'], $data['old_password']);
        }

        // Changing email
        if (isset($data['email'])) {
            if ($data['email'] === $editedUser['email']) throw new HttpBadRequestException($request, 'Your new email have to be diffrent than Your current email');

            if ($this->User->exist(['email' => $data['email']])) {
                throw new HttpConflictException('Given mail ' . $data['email'] . ' already exist.');
            }
            /* Just sending an action_key to user on his new email, but nod updating email */

            $editedUser['action_key'] = $this->getRandomKey(6);

            $MailSender = $this->DIcontainer->get(MailSender::class);
            $MailSender->setUser($editedUser);
            $MailSender->setMailSubject('Change email');
            $MailSender->send();
            
            $this->User->update(['action_key' => $editedUser['action_key']]);
            $this->Log->create([
                'user_id' => $currentUser,
                'message' => 'User $userEmail (id=' . $editedUser['id'] . ') want to change mail - updated with data:' . json_encode($data)
            ]);
            $data['new_email'] = $data['email'];
            unset($data['email']);
        }

        if (!empty($data)) {
            $this->User->update($data);
            $this->Log->create([
                'user_id' => $currentUser,
                'message' => 'USER ' . $userEmail . ' UPDATE user ' . $editedUser['email'] . ' DATA ' . json_encode($data)
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
        $deletedUser = $this->User->read(['id' => $args['userID']])[0];
        unset($deletedUser['password']);

        $this->User->delete((int) $args['userID']);
        $this->Log->create([
            'user_id' => $args['userID'],
            "message" => 'USER ' . $request->getAttribute('email') . ' DELETE user DATA ' . json_encode($deletedUser)
        ]);

        return $response->withStatus(204, "Deleted");
    }
}
