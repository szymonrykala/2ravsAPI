<?php

namespace controllers;

use models\HttpConflictException;
use models\HttpNotFoundException;
use Nowakowskir\JWT\JWT;
use Nowakowskir\JWT\TokenDecoded;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;


class UserController extends Controller
{
    private $User;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->User = $this->DIcontainer->get('User');
    }

    private function generateToken(int $userID, int $accessID, string $email): string
    {
        //creating new token
        $time = time();
        $tokenDecoded = new TokenDecoded(
            ['typ' => 'JWT', 'alg' => JWT::ALGORITHM_HS384],
            array(
                'user_id' => $userID,
                'access_id' => $accessID,
                'email' => $email,
                'ex' => $time + (60 * 60) * 48 //valid 48h
            )
        );
        // encoding the token
        $tokenEncoded = $tokenDecoded->encode($this->DIcontainer->get('settings')['jwt']['secret'], JWT::ALGORITHM_HS384);
        return $tokenEncoded->__toString();
    }

    private function getRandomKey(int $len): string
    {
        return base64_encode(random_bytes($len));
    }

    public function validateUser(Request $request, array &$data): void
    {
        /**
         * Validate User
         * 
         * @param array $data
         * @throws HttpBadRequestException
         */
        $Validator = $this->DIcontainer->get('Validator');
        foreach (['name', 'surname'] as $item) {
            if (isset($data[$item])) {
                if (!$Validator->validateClearString($data[$item])) {
                    throw new HttpBadRequestException($request, 'Incorrect user ' . $item . ' value; pattern: ' . $Validator->clearString);
                }
            }
        }

        foreach (['password', 'old_password', 'repeat_password'] as $item) {
            if (isset($data[$item])) {
                if (!$Validator->validatePassword($data[$item])) {
                    throw new HttpBadRequestException($request, 'Incorrect user ' . $item . ' format; pattern: ' . $Validator->password);
                }
            }
        }

        if (isset($data['email']) && !$Validator->validateEmail($data['email'])) {
            throw new HttpBadRequestException($request, 'Incorrect user email format');
        }
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
        ) = $this->getFrom($request, [
            'email' => 'string',
            'password' => 'string'
        ]);

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        try {
            list(
                'password' => $userPassword,
                'id' => $userID,
                'access_id' => $accessID,
                'login_fails' => $loginFails,
                'activated' => $activated
            ) = $this->User->read(['email' => $email])[0];
        } catch (HttpNotFoundException $e) {
            throw new HttpBadRequestException($request, "Can not login. Given email '$email' is not exist");
        }

        if ((bool)$activated === false) {
            throw new HttpForbiddenException($request, "Can not authenticate because user is not activated"); //confilct
        }

        if ($loginFails >= 5) {
            throw new HttpForbiddenException($request, "Can not login. Login failed to many times and Your account is locked. Please contact with Your administrator");
        }

        if (password_verify($password, $userPassword)) {
            $Access = $this->DIcontainer->get('Access');
            $data = array(
                "jwt" => $this->generateToken($userID, $accessID, $email),
                'userID' => $userID,
                "access" => $Access->read(['id' => $accessID])
            );
            $this->User->update($userID, array('login_fails' => 0));
            $this->Log->create(array(
                'user_id' => $userID,
                'message' => "User $email succesfully veryfied"
            ));
        } else {
            $loginFails += 1;
            $this->User->update($userID, array('login_fails' => $loginFails));
            $this->Log->create(array(
                'user_id' => $userID,
                'message' => "User $email veryfing failed count:$loginFails"
            ));
            throw new HttpBadRequestException($request, "Authentication failed (count:$loginFails). Password is not correct.");
        }

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
        list(
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'password' => $password,
            'repeat_password' => $repeat_password
        ) = $this->getFrom($request, array(
            'email' => 'string',
            'password' => 'string',
            'repeat_password' => 'string',
            'name' => 'string',
            'surname' => 'string'
        ), true);

        if ($password !== $repeat_password) throw new HttpBadRequestException($request, 'Given password and repeat_password are not the same');

        $userData = [
            'name' => $name,
            'surname' => $surname,
            'password' => $password,
            'email' => $email,
            'action_key' => $this->getRandomKey(6)
        ];

        $this->validateUser($request, $userData);


        $MailSender = $this->DIcontainer->get('MailSender');
        $MailSender->setUser($userData);
        $MailSender->setMailSubject('User Activation');
        $MailSender->send();

        $userID = $this->User->create($userData);
        unset($userData['password']);
        $this->Log->create(array(
            'user_id' => $userID,
            'message' => "User $email has been registered data:" . json_encode($userData)
        ));

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
         *      "activation_key" : "",
         *      "action" : "resend" | "activate | change_email"
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        ['password' => $password, 'email' => $email, 'key' => $key, 'action' => $action] = $this->getFrom($request, [
            'password' => 'string',
            'email' => 'string',
            'key' => 'string',
            'action' => 'string'
        ], true);

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

        switch ($action) {
            case 'resend':
                $user['action_key'] = $this->getRandomKey(6);
                $this->User->update($user['id'], ['action_key' => $user['action_key']]);

                $MailSender = $this->DIcontainer->get('MailSender');
                $MailSender->setUser($user);
                $MailSender->setMailSubject('User Activation');
                $MailSender->send();

                $this->Log->create([
                    'user_id' => $user['id'],
                    'message' => 'Account user ' . $user['email'] . 'was requested new activation email'
                ]);

                $response->getBody()->write(json_encode('Your Code has been resended'));
                break;

            case 'activate':
                if ($user['action_key'] !== $key) {
                    throw new HttpBadRequestException($request, 'Your activation key is not correct');
                }
                $this->User->update($user['id'], ['activated' => 1, 'action_key' => '1']);
                $this->Log->create([
                    'user_id' => $user['id'],
                    'message' => 'Account user ' . $user['email'] . 'was activated'
                ]);

                $response->getBody()->write(json_encode('User succesfully activated'));
                break;

            case 'change_email':
                /* given email is new user email - it's not set yet*/
                $editedEmail = ['email' => $email];
                if ($this->User->exist($editedEmail)) {
                    // throw new HttpConflictException('Given email ' . $email . ' already exist. Someone activated the same email before You.');
                    throw new HttpConflictException('Given email ' . $email . ' already exist. Someone activated the same email before You.');
                }
                $this->validateUser($request, $editedEmail);
                $editedEmail['action_key'] = 'NONE_NONE';
                // setting new email
                $this->User->update($user['id'], $editedEmail);
                $this->Log->create([
                    'user_id' => $user['id'],
                    'message' => 'User ' . $user['email'] . 'changed his email to ' . $email
                ]);
                $response->getBody()->write(json_encode('Email changed to ' . $email));
                break;
            default:
                break;
        }
        return $response->withStatus(200);
    }

    public function resendActivationEmail(Request $request, Response $response, $args): Response
    {
        throw new HttpNotImplementedException($request, "UserController::resendActivationEmail not implemented");
        return $response;
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

        $data = $this->getFrom($request, [
            'email' => 'string',
            'old_password' => 'string',
            'new_password' => 'string',
            'name' => 'string',
            'surname' => 'string',
            'access_id' => 'integer'
        ], false);

        $this->validateUser($request, $data);

        $currentUser = (int) $request->getAttribute('user_id');
        $accessID = $request->getAttribute('access_id');
        $userEmail = $request->getAttribute('email');

        // checking if user can change acces
        if (isset($data['access_id'])) {
            $Access = $this->DIcontainer->get("Access");
            if (
                (bool)$Access->read(['id' => $accessID])[0]['access_edit'] === false
            ) throw new HttpUnauthorizedException($request, 'You do not have acces to edit user access_id');
        }

        $editedUser = $this->User->read(['id' => $args['userID']])[0];

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
            if ($data['email'] === $editedUser['email']) throw new HttpBadRequestException($request, "Your new email have to be diffrent than Your current email");

            if ($this->User->exist(['email' => $data['email']])) {
                throw new HttpConflictException('Given mail ' . $data['email'] . ' already exist.');
            }
            /* Just sending an action_key to user on his new email, but nod updating email */

            $editedUser['action_key'] = $this->getRandomKey(6);

            $MailSender = $this->DIcontainer->get('MailSender');
            $MailSender->setUser($editedUser);
            $MailSender->setMailSubject('User Activation');
            $MailSender->send();

            $this->User->update($editedUser['id'], ['action_key' => $editedUser['action_key']]);
            $this->Log->create([
                'user_id' => $currentUser,
                'message' => "User $userEmail (id=" . $editedUser['id'] . ") want to change mail - updated with data:" . json_encode($data)
            ]);
            unset($data['email']);
        }

        if (!empty($data)) {
            $this->User->update($editedUser['id'], $data);
            $this->Log->create([
                'user_id' => $currentUser,
                'message' => "Updated User $userEmail (id=" . $editedUser['id'] . ") with data:" . json_encode($data)
            ]);
        }

        return $response->withStatus(204, "Updated");
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
        $deletedUser = (int) $args['userID'];
        $userEmail = $request->getAttribute('email');

        list('email' => $deletedUserEmail) = $this->User->read(['id' => $deletedUser])[0];

        $this->User->delete($deletedUser);
        $this->Log->create([
            'user_id' => $deletedUser,
            "message" => "User $userEmail deleted $deletedUserEmail"
        ]);

        return $response->withStatus(204, "Deleted");
    }
}
