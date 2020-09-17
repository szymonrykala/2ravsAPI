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


    public function getAllAccesTypes(Request $request, Response $response, $args): Response
    {
        /**
         * Getting acces types from database
         * wirites array of items to json body
         * // GET /acces
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        $data = $this->Acces->read();
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    public function getAccesTypeByID(Request $request, Response $response, $args): Response
    {
        /**
         * Getting acces type from database
         * wirites one item to json body
         * // GET /acces/{acces_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        $data = $this->Acces->read(['id' => $args['acces_id']])[0];
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    public function createNewAccesType(Request $request, Response $response, $args): Response
    {
        /**
         * Creating acces type in database
         * POST /acces/{acces_id}
         * {
         *     "name":"",
         *     "acces_edit":"",
         *     "buildings_view":"",
         *     "buildings_edit":"",
         *     "logs_view":"",
         *     "logs_edit":"",
         *     "rooms_view":"",
         *     "rooms_edit":"",
         *     "reservations_acces":"",
         *     "reservations_confirm":"",
         *     "reservations_edit":"",
         *     "users_edit":"",
         *     "statistics_view":""
         * } 
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
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
        return $response->withStatus(201,"Created");
    }

    public function updateAccesType(Request $request, Response $response, $args): Response
    {
        /**
         * Updating acces type by given acces_id
         * PATCH /acces/{acces_id}
         * {
         *     "name":"",
         *     "acces_edit":"",
         *     "buildings_view":"",
         *     "buildings_edit":"",
         *     "logs_view":"",
         *     "logs_edit":"",
         *     "rooms_view":"",
         *     "rooms_edit":"",
         *     "reservations_acces":"",
         *     "reservations_confirm":"",
         *     "reservations_edit":"",
         *     "users_edit":"",
         *     "statistics_view":""
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        $data = $this->getFrom($request);
        $this->Acces->update($args['acces_id'], $data);
        $this->Log->create([
            "user_id" => $request->getAttribute('user_id'),
            "message" => "User " . $request->getAttribute('email') . " updated"
        ]);
        return $response->withStatus(204, "Updated");
    }

    public function deleteAccesType(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting acces type by acces_id
         * DELETE /acces/{acces_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
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
        return $response->withStatus(204, "Deleted");
    }
}
