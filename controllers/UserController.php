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
            throw new HttpBadRequestException($request, "Can not login. User with email '$email' do not exist");
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
         *    "password":{"type":"string","min":10,"max":20}
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
            'password' => $password
        ) = $this->getFrom($request, array(
            'email' => 'string',
            'password' => 'string',
            'name' => 'string',
            'surname' => 'string'
        ));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new HttpBadRequestException($request, "Given email is not in correct format.");
        }

        $passLen = strlen($password);
        preg_match_all('/[0-9]/', $password, $numsCount);
        if ($passLen < 10 || $passLen > 20) {
            throw new HttpBadRequestException($request, "Password length is not acceptable (min=10, max=20)");
        } elseif (strpos(' ', $password)) {
            throw new HttpBadRequestException($request, "Password can not contain spaces");
        } elseif (strpos('<', $password) || strpos('>', $password)) {
            throw new HttpBadRequestException($request, "Password can not contain '<' and '>' ");
        } elseif (count($numsCount[0]) < 4) {
            throw new HttpBadRequestException($request, "Password need to contain minimum 4 digits");
        }

        $randomKey = $this->getRandomKey(60);

        $userData = [
            'name' => $name,
            'surname' => $surname,
            'password' => $password,
            'email' => $email,
            'action_key' => $randomKey
        ];
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
        $this->User->setQueryStringParams($this->parsedQueryString($request));

        if (isset($args['userID'])) {
            $args['id'] = $args['userID'];
            unset($args['userID']);
        }
        $data = $this->handleExtensions($this->User->read($args), $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // GET users/search
    public function searchUsers(Request $request, Response $response, $args): Response
    {
        /**
         * Searching for users with parameters given in Request(query string or body['search'])
         * Founded results are written into the response body
         * GET /logs/search?<queryString>
         * { "search":{"key":"value","key2":"value2"}}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $params = $this->getSearchParams($request);

        $data = $this->User->search($params);

        $data = $this->handleExtensions($data, $request);
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
         *    "password":{string},
         *    "new_password":{string},
         * } 
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $qData = $this->getFrom($request);
        $currentUser = (int) $request->getAttribute('user_id');
        $editedUser = $args['userID'];
        $accessID = $request->getAttribute('access_id');
        $userEmail = $request->getAttribute('email');

        $data = [];
        if (isset($qData['name'])) {
            $data['name'] = $qData['name'];
        }

        if (isset($qData['surname'])) {
            $data['surname'] = $qData['surname'];
        }

        if (isset($qData['email']) && $qData['email'] !== $userEmail) {
            if (!filter_var($qData['email'], FILTER_VALIDATE_EMAIL)) {
                throw new HttpBadRequestException($request, 'email is not correct format');
            }
            $data['email'] = $qData['email'];
        }

        if (
            (isset($qData['password']) && !isset($qData['new_password'])) ||
            (!isset($qData['password']) && isset($qData['new_password']))
        ) {
            throw new HttpBadRequestException($request, "When updateing password, old password is needed to");
        } elseif (isset($qData['password']) && isset($qData['new_password'])) {

            list('password' => $passwordHash) = $this->User->read(['id' => $editedUser])[0];

            if (password_verify($qData['password'], $passwordHash)) {
                $password = $qData['new_password'];

                $passLen = strlen($password);
                preg_match_all('/[0-9]/', $password, $numsCount);
                if ($passLen < 10 || $passLen > 20) {
                    throw new HttpBadRequestException($request, 'unwanted password length (min=10, max=20)');
                } elseif (strpos(' ', $password)) {
                    throw new HttpBadRequestException($request, 'unwanted spaces in password');
                } elseif (count($numsCount[0]) < 4) {
                    throw new HttpBadRequestException($request, 'password requires 4 digits');
                }
                $options = [
                    'cost' => 12,
                ];
                $data['password'] = password_hash($password, PASSWORD_BCRYPT, $options);
            }
        }

        $this->User->update($editedUser, $data);
        unset($data['password']);
        $this->Log->create(['user_id' => $currentUser, 'message' => "User $userEmail (id=$currentUser) updated user (id=$editedUser) data:" . json_encode($data)]);

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
