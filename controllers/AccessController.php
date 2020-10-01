<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";

class AccessController extends Controller
{
    private $Access;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Access = $DIcontainer->get('Access');
    }


    public function getAllAccessTypes(Request $request, Response $response, $args): Response
    {
        /**
         * Getting access types from database
         * wirites array of items to json body
         * // GET /access
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        $data = $this->Access->read();
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    public function getAccessTypeByID(Request $request, Response $response, $args): Response
    {
        /**
         * Getting access type from database
         * wirites one item to json body
         * // GET /access/{access_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        $data = $this->Access->read(['id' => $args['access_id']])[0];
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    public function createNewAccessType(Request $request, Response $response, $args): Response
    {
        /**
         * Creating access type in database
         * POST /access/{access_id}
         * {
         *     "name":"",
         *     "access_edit":"",
         *     "buildings_view":"",
         *     "buildings_edit":"",
         *     "logs_view":"",
         *     "logs_edit":"",
         *     "rooms_view":"",
         *     "rooms_edit":"",
         *     "reservations_access":"",
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
            "access_edit" => 'boolean',
            "buildings_view" => 'boolean',
            "buildings_edit" => 'boolean',
            "logs_view" => 'boolean',
            "logs_edit" => 'boolean',
            "rooms_view" => 'boolean',
            "rooms_edit" => 'boolean',
            "reservations_access" => 'boolean',
            "reservations_confirm" => 'boolean',
            "reservations_edit" => 'boolean',
            "users_edit" => 'boolean',
            "statistics_view" => 'boolean',
        ]);
        //name policy
        if (strlen($data["name"]) < 4) {
            throw new Exception("Access name need to have at least 4 characters", 400);
        }

        $newID = $this->Access->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            "message" => "User " . $request->getAttribute('email') . " created new access class data:".json_encode($data)
        ]);
        return $response->withStatus(201, "Created");
    }

    public function updateAccessType(Request $request, Response $response, $args): Response
    {
        /**
         * Updating access type by given access_id
         * PATCH /access/{access_id}
         * {
         *     "name":"",
         *     "access_edit":"",
         *     "buildings_view":"",
         *     "buildings_edit":"",
         *     "logs_view":"",
         *     "logs_edit":"",
         *     "rooms_view":"",
         *     "rooms_edit":"",
         *     "reservations_access":"",
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
        $this->Access->update($args['access_id'], $data);
        $this->Log->create([
            "user_id" => $request->getAttribute('user_id'),
            "message" => "User " . $request->getAttribute('email') . " updated data:".json_encode($data)
        ]);
        return $response->withStatus(204, "Updated");
    }

    public function deleteAccessType(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting access type by access_id
         * DELETE /access/{access_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $args
         * 
         * @return Response $response
         */
        // each user with current access have to 
        $User = $this->DIcontainer->get('User');
        if ($User->exist(['access_id' => $args['access_id']])) {
            throw new Exception("Some Users stil have this access class. You can't delete it", 409); //conflict
        } else {
            $this->Access->delete($args['access_id']);
            $this->Log->create([
                "user_id" => $request->getAttribute('user_id'),
                "message" => "User " . $request->getAttribute('email') . " deleted access id=" . $args['access_id']
            ]);
        }
        return $response->withStatus(204, "Deleted");
    }
}