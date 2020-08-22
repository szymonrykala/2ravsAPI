<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";



// class Vieww
// {
//     public $response = null;
//     public function __construct()
//     {
//         $this->response = new Response();
//         $this->response->withHeader('content-type', 'application/json');
//     }

//     public function set(array $data, int $code)
//     {
//     }

//     public function response(){
//         return $this->response->withStatus($this->d);
//     }
// }

class UserController extends Controller
{
    private $User;
    private $Acces;
    // private $View;
    protected $DIcontainer;

    public function __construct(ContainerInterface $DIcontainer)
    {
        $this->DIcontainer = $DIcontainer;
        $this->User = $DIcontainer->get('User');
        $this->Log = $DIcontainer->get("Log");
        // $this->View = new View();
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
                'login_fails' => $loginFails
            ) = $this->User->read(array('email' => $email))[0];
        } catch (NothingFoundException $e) {
            throw new AuthenticationException('(email)');
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

        $response->getBody()->write("rejestracja");
        return $response;
    }

    //##########################
    public function activateUser(Request $request, Response $response, $args): Response
    {

        $response->getBody()->write("User controller");
        return $response;
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

    public function getAllUsers(Request $request, Response $response, $args): Response
    {

        $response->getBody()->write("User controller");
        return $response;
    }

    public function getSpecificUser(Request $request, Response $response, $args): Response
    {

        $response->getBody()->write("User controller");
        return $response;
    }

    public function updateUserInformations(Request $request, Response $response, $args): Response
    {

        $response->getBody()->write("User controller");
        return $response;
    }

    public function deleteUser(Request $request, Response $response, $args): Response
    {

        $response->getBody()->write("User controller");
        return $response;
    }
}
