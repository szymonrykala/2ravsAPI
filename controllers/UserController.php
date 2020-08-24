<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";


class UserController extends Controller
{
    private $User;
    private $Acces;
    protected $DIcontainer;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->User = $this->DIcontainer->get('User');
    }

    /* {
        email:{"type":"string"}
        password:{"type":"string"}
    } */
    public function verifyUser(Request $request, Response $response, $args): Response
    {
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
                'acces_id' => $accesID,
                'login_fails' => $loginFails,
                'activated' => $activated
            ) = $this->User->read(array('email' => $email))[0];
        } catch (NothingFoundException $e) {
            throw new AuthenticationException('(email)');
        }

        if ((bool)$activated === false) {
            throw new ActivationException("User account is not activated");
        }

        if ($loginFails >= 5) {
            throw new AuthenticationFailsCountException($loginFails);
        }

        if (password_verify($password, $userPassword)) {
            $data = array("jwt" => $this->generateToken($userID, $accesID, $email));
            $this->User->update($userID, array('login_fails' => 0));
            $this->Log->create(array(
                'user_id' => $userID,
                'message' => "User $email succesfully veryfied"
            ));
        } else {
            $this->User->update($userID, array('login_fails' => $loginFails + 1));
            $this->Log->create(array(
                'user_id' => $userID,
                'message' => "User $email veryfing failed"
            ));
            throw new AuthenticationException("password is not correct");
        }

        $response->getBody()->write(json_encode($data));
        // $response->getBody()->write(json_encode($data));
        return $response;
    }

    /* {
        "name":{"type":"string"},
        "surname":{"type":"string"},
        "email":{"type":"string"},
        "password":{"type":"string","min":10,"max":20}
    } */
    public function registerNewUser(Request $request, Response $response, $args): Response
    {
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
            throw new CredentialsPolicyException('email is not correct format');
        }

        $passLen = strlen($password);
        preg_match_all('/[0-9]/', $password, $numsCount);
        if ($passLen < 10 || $passLen > 20) {
            throw new CredentialsPolicyException('unwanted password length (min=10, max=20)');
        } elseif (strpos(' ', $password)) {
            throw new CredentialsPolicyException('unwanted spaces in password');
        } elseif (count($numsCount[0]) < 4) {
            throw new CredentialsPolicyException('password requires 4 digits');
        }

        $randomKey = $this->getRandomKey(60);

        $userID = $this->User->create(array(
            'name' => $name,
            'surname' => $surname,
            'password' => $password,
            'email' => $email,
            'action_key' => $randomKey
        ));
        $this->Log->create(array(
            'user_id' => $userID,
            'message' => "User $email has been registered"
        ));

        /* Mail->register($key)->sendTo($email); */

        // $response->getBody()->write("");
        return $response->withStatus(201, "User registered");
    }

    //##########################
    // /users/activate?key=<string key>
    public function activateUser(Request $request, Response $response, $args): Response
    {
        $key = $this->getQueryParam($request, 'key')[0];

        if (!$this->User->exist(['action_key' => $key])) {
            //if user with given key was not found
            throw new ActivationException('no such activation key, or user already activated');
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
        // $response->getBody();
        return $response->withStatus(201, "User activated");
    }

    public function resendActivationEmail(Request $request, Response $response, $args): Response
    {

        $response->getBody()->write("User controller");
        return $response;
    }
    //###########################

    public function logoutUser(Request $request, Response $response, $args): Response
    {

        $response->getBody()->write("User controller");
        return $response;
    }

    // GET /users?ext=<acces_id>
    public function getAllUsers(Request $request, Response $response, $args): Response
    {
        $ext = $this->getQueryParam($request, 'ext');
        if (in_array('acces_id', $ext)) {
            $Acces = $this->DIcontainer->get('Acces');
        }

        $users = $this->User->read();
        foreach ($users as &$user) {
            if (in_array('acces_id', $ext)) {
                $user['acces'] = $Acces->read(['id' => $user['acces_id']])[0];
                unset($user['acces_id']);
            }
            unset($user['password']);
            unset($user['action_key']);
        }
        $response->getBody()->write(json_encode($users));
        return $response->withStatus(200);
    }

    // GET /users/{user_id}?ext=<acces_id>
    public function getSpecificUser(Request $request, Response $response, $args): Response
    {
        $userID = $args['userID'];
        $ext = $this->getQueryParam($request, 'ext');
        if (in_array('acces_id', $ext)) {
            $Acces = $this->DIcontainer->get('Acces');
        }

        $user = $this->User->read(['id' => $userID])[0];

        if (in_array('acces_id', $ext)) {
            $user['acces'] = $Acces->read(['id' => $user['acces_id']])[0];
            unset($user['acces_id']);
        }
        unset($user['password']);
        unset($user['action_key']);

        $response->getBody()->write(json_encode($user));
        return $response->withStatus(200);
    }

    //some data have to be passed else "bad request"
    /* optional: 
    {
        "name":{string},
        "surname":{string},
        "email":{string}
        "password":{string},
        "new_password":{string},
    } */
    public function updateUserInformations(Request $request, Response $response, $args): Response
    {
        $qData = $this->getFrom($request);
        $currentUser = (int) $request->getAttribute('user_id');
        $editedUser = $args['userID'];
        $accesID = $request->getAttribute('acces_id');
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
                throw new CredentialsPolicyException('email is not correct format');
            }
            $data['email'] = $qData['email'];
        }

        if (
            (isset($qData['password']) && !isset($qData['new_password'])) ||
            (!isset($qData['password']) && isset($qData['new_password']))
        ) {
            throw new RequiredParameterException(["password" => 0, "new_password" => 1]);
        } elseif (isset($qData['password']) && isset($qData['new_password'])) {

            list('password' => $passwordHash) = $this->User->read(['id' => $editedUser])[0];

            if (password_verify($qData['password'], $passwordHash)) {
                $password = $qData['new_password'];

                $passLen = strlen($password);
                preg_match_all('/[0-9]/', $password, $numsCount);
                if ($passLen < 10 || $passLen > 20) {
                    throw new CredentialsPolicyException('unwanted password length (min=10, max=20)');
                } elseif (strpos(' ', $password)) {
                    throw new CredentialsPolicyException('unwanted spaces in password');
                } elseif (count($numsCount[0]) < 4) {
                    throw new CredentialsPolicyException('password requires 4 digits');
                }
                $options = [
                    'cost' => 12,
                ];
                $data['password'] = password_hash($password, PASSWORD_BCRYPT, $options);
            }
        }
        $dataString = implode(',', array_keys($data));
        $this->User->update($editedUser, $data);
        $this->Log->create(['user_id' => $currentUser, 'message' => "User $userEmail (id=$currentUser) updated user (id=$editedUser) data: $dataString"]);
        // $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    public function deleteUser(Request $request, Response $response, $args): Response
    {
        $deletedUser = (int) $args['userID'];
        $userEmail = $request->getAttribute('email');

        list('email' => $deletedUserEmail) = $this->User->read(['id' => $deletedUser])[0];

        $this->User->delete($deletedUser);
        $this->Log->create([
            'user_id' => $deletedUser,
            "message" => "User $userEmail deleted $deletedUserEmail"
        ]);

        $response->getBody()->write("User controller");
        return $response;
    }
}
