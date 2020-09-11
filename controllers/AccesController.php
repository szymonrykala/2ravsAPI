<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";

class AccesController extends Controller
{
    private $Acces;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Acces = $DIcontainer->get('Acces');
    }

    // POST /acces/{acces_id}
    /* {
        "name":"",
        "acces_edit":"",
        "buildings_view":"",
        "buildings_edit":"",
        "logs_view":"",
        "logs_edit":"",
        "rooms_view":"",
        "rooms_edit":"",
        "reservations_acces":"",
        "reservations_confirm":"",
        "reservations_edit":"",
        "users_edit":"",
        "statistics_view":""
    } */
    public function createNewAccesType(Request $request, Response $response, $args): Response
    {
        $data = $this->getFrom($request, [
            "name" => 'string',
            "acces_edit" => 'boolean',
            "buildings_view" => 'boolean',
            "buildings_edit" => 'boolean',
            "logs_view" => 'boolean',
            "logs_edit" => 'boolean',
            "rooms_view" => 'boolean',
            "rooms_edit" => 'boolean',
            "reservations_acces" => 'boolean',
            "reservations_confirm" => 'boolean',
            "reservations_edit" => 'boolean',
            "users_edit" => 'boolean',
            "statistics_view" => 'boolean',
        ]);
        //name policy
        if (strlen($data["name"]) < 4) {
            throw new APIException("Acces name need to have at least 4 characters", 400);
        }

        $newID = $this->Acces->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            "message" => "User " . $request->getAttribute('email') . " created new acces class '" . $data['name'] . "' id=$newID "
        ]);
        $response->getBody()->write("Created");
        return $response;
    }

    // GET /acces
    public function getAllAccesTypes(Request $request, Response $response, $args): Response
    {
        $data = $this->Acces->read();
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET /acces/{acces_id}
    public function getAccesTypeByID(Request $request, Response $response, $args): Response
    {
        $data = $this->Acces->read(['id' => $args['acces_id']])[0];
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // PATCH /acces/{acces_id}
    /* {
        "name":"",
        "acces_edit":"",
        "buildings_view":"",
        "buildings_edit":"",
        "logs_view":"",
        "logs_edit":"",
        "rooms_view":"",
        "rooms_edit":"",
        "reservations_acces":"",
        "reservations_confirm":"",
        "reservations_edit":"",
        "users_edit":"",
        "statistics_view":""
    } */
    public function updateAccesType(Request $request, Response $response, $args): Response
    {
        $data = $this->getFrom($request);
        $this->Acces->update($args['acces_id'], $data);
        $this->Log->create([
            "user_id" => $request->getAttribute('user_id'),
            "message" => "User ".$request->getAttribute('email')." updated"
        ]);
        return $response;
    }

    // DELETE /acces/{acces_id}
    public function deleteAccesType(Request $request, Response $response, $args): Response
    {
        // each user with current acces have to 
        $User = $this->DIcontainer->get('User');
        if ($User->exist(['acces_id' => $args['acces_id']])) {
            throw new APIException("Some Users stil have this acces class. You can't delete it", 400);
        } else {
            $this->Acces->delete($args['acces_id']);
            $this->Log->create([
                "user_id" => $request->getAttribute('user_id'),
                "message" => "User " . $request->getAttribute('email') . " deleted acces id=" . $args['acces_id']
            ]);
        }

        $response->getBody()->write("Deleted");
        return $response;
    }
}
