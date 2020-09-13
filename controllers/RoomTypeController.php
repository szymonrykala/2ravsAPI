<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";

class RoomTypeController extends Controller
{
    /**
     * Implement endpoints related with buildings/rooms/types paths
     * 
     */
    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Type = $this->DIcontainer->get('RoomType');
    }

    public function getAllTypes(Request $request, Response $response, $args): Response
    {
        $data = $this->Type->read();
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    public function createType(Request $request, Response $response, $args): Response
    {
        $data = $this->getFrom($request, ["name" => "string"]);
        $lastIndex = $this->Type->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => "User " . $request->getAttribute('email') . " created new room type id=$lastIndex"
        ]);
        return $response->withStatus(204, "Succesfully created");
    }

    public function updateType(Request $request, Response $response, $args): Response
    {
        $typeID = (int)$args['room_type_id'];

        $data = $this->getFrom($request);
        $this->Type->update($typeID, $data);

        $dataString = implode(',', array_keys($data));
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => "User " . $request->getAttribute('email') . " updated room type id=" . $typeID . " data: $dataString"
        ]);
        return $response->withStatus(204, "Succesfully updated");
    }

    public function deleteType(Request $request, Response $response, $args): Response
    {
        $typeID = (int)$args['room_type_id'];
        $this->Type->delete($typeID);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => "User " . $request->getAttribute('email') . " updated room type id=" . $typeID
        ]);
        return $response->withStatus(204, "Succesfully deleted");
    }
}
