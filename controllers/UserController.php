<?php

use Nowakowskir\JWT\JWT;
use Nowakowskir\JWT\TokenDecoded;
use Opis\Closure\SecurityException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;

require_once __DIR__ . "/Controller.php";


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
        $tokenEncoded = $tokenDecoded->encode(JWT_SIGNATURE, JWT::ALGORITHM_HS384);
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
            throw new HttpBadRequestException($request, 'Incorrect user email value');
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
        ) = $this->getFrom($request, array(
            'email' => 'string',
            'password' => 'string'
        ));

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        try {
            list(
                'password' => $userPassword,
                'id' => $userID,
                'access_id' => $accessID,
                'login_fails' => $loginFails,
                'activated' => $activated
            ) = $this->User->read(array('email' => $email))[0];
        } catch (LengthException $e) {
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
            'action_key' => $this->getRandomKey(60)
        ];

        $this->validateUser($request, $userData);
        
        $userID = $this->User->create($userData);
        unset($userData['password']);
        $this->Log->create(array(
            'user_id' => $userID,
            'message' => "User $email has been registered data:" . json_encode($userData)
        ));

        /* Mail->register($key)->sendTo($email); */

        // $response->getBody()->write("");
        return $response->withStatus(201, "Created");
    }

    // GET /users/activate?key=<string key>
    public function activateUser(Request $request, Response $response, $args): Response
    {
        /**
         * Activating user and redirect user to given url
         * GET /users/activate?key=<string key>
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $key = $this->parsedQueryString($request, 'key');

        if (empty($this->User->read(['action_key' => $key])[0])) {
            //if user with given key was not found
            throw new HttpBadRequestException($request, 'Given activation key is not exist');
        }

        list(
            'id' => $userID,
            'email' => $email,
        ) = $this->User->read(['action_key' => $key])[0];

        $this->User->update($userID, ['activated' => 1, 'action_key' => '1']);
        $this->Log->create([
            'user_id' => $userID,
            'message' => "Account user $email was activated"
        ]);

        //redirect on given address
        return $response;
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

        if (isset($data['old_password'], $data['new_password'])) {
            if ($data['old_password'] === $data['new_password']) {
                throw new HttpBadRequestException($request, 'Incorrect passowrds values - old_password and new_password can not be the same');
            }
            if (password_verify($data['old_password'], $editedUser['password'])) {
                $data['password'] = password_hash($data['new_password'], PASSWORD_BCRYPT, ['cost' => 12]);
            }
        }

        unset($data['new_password'], $data['old_password']);

        $this->User->update($editedUser['id'], $data);
        unset($data['password']);
        $this->Log->create([
            'user_id' => $currentUser,
            'message' => "Updated User $userEmail (id=" . $editedUser['id'] . ") with data:" . json_encode($data)
        ]);

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
