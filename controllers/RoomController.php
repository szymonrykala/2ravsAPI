<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\InvalidArgumentException;

require_once __DIR__ . "/Controller.php";

class RoomController extends Controller
{
    /**
     * Responsible for operation with /rooms table in database
     */
    protected $DIcontainer;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Room = $this->DIcontainer->get('Room');
    }
    // GET /buildings/{building_id}/rooms
    public function getAllRoomsInBuilding(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all rooms in specific building 
         * 
         * @param Request $request 
         * @param Response $response
         * @param array $args
         * 
         * @return Response 
         */
        $buildingID = (int)$args['building_id'];
        $Type = $this->DIcontainer->get('RoomType');

        $rooms = $this->Room->read(['building_id' => $buildingID]);
        foreach ($rooms as &$room) {
            $room['room_type'] = $Type->read(['id' => $room['room_type_id']])[0];
            unset($room['room_type_id']);
        }
        $data = $this->handleExtensions($rooms, $request);
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET /buildings/{building_id}/rooms/{room_id}
    public function getRoomByID(Request $request, Response $response, $args): Response
    {
        /**
         * Getting specific room in specific building from database
         * 
         * @param Request $request 
         * @param Response $response
         * @param array $args
         * 
         * @return Response 
         */
        $buildingID = (int)$args['building_id'];
        $roomID = (int)$args['room_id'];
        $Type = $this->DIcontainer->get('RoomType');
        $Building = $this->DIcontainer->get('Building');

        $room = $this->Room->read(['building_id' => $buildingID, 'id' => $roomID])[0];

        $room['room_type'] = $Type->read(['id' => $room['room_type_id']])[0];
        unset($room['room_type_id']);

        $room['building'] = $Building->read(['id' => $room['building_id']])[0];
        unset($room['building_id']);

        $response->getBody()->write(json_encode($room));
        return $response;
    }

    // GET /buildings/rooms/{room_id}
    public function getRoom(Request $request, Response $response, $args): Response
    {
        /**
         * Getting specific room form
         * 
         * @param Request $request 
         * @param Response $response
         * @param array $args
         * 
         * @return Response 
         */
        $roomID = (int)$args['room_id'];
        $Type = $this->DIcontainer->get('RoomType');
        $Building = $this->DIcontainer->get('Building');

        $room = $this->Room->read(['id' => $roomID])[0];

        $room['room_type'] = $Type->read(['id' => $room['room_type_id']])[0];
        unset($room['room_type_id']);

        $room['building'] = $Building->read(['id' => $room['building_id']])[0];
        unset($room['building_id']);

        $data = $this->handleExtensions($room, $request);
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET /buildings/rooms
    public function getAllRooms(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all rooms in database
         * 
         * @param Request $request 
         * @param Response $response
         * @param array $args
         * 
         * @return Response 
         */
        $rooms = $this->Room->read();
        $Type = $this->DIcontainer->get('RoomType');

        foreach ($rooms as &$room) {
            $room['room_type'] = $Type->read(['id' => $room['room_type_id']])[0];
            unset($room['room_type_id']);
        }

        $data = $this->handleExtensions($rooms, $request);
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    public function createRoom(Request $request, Response $response, $args): Response
    {
        /**
         * creating room in specified building
         * POST /buildings/{building_id}/rooms
         * {
         *      "name":"",
         *      "room_type_id":1,
         *      "seats_count":1,
         *      "floor":1,
         *      "equipment":"umywalka,kreda,tablica"
         * }
         * @param Request $request 
         * @param Response $response
         * @param array $args
         * 
         * @return Response 
         */
        $buildingID = (int) $args['building_id'];
        $data = $this->getFrom($request, [
            'name' => "string",
            'room_type_id' => 'integer',
            'seats_count' => 'integer',
            'floor' => 'integer',
            'equipment' => 'string'
        ]);
        $data['building_id'] = $buildingID;
        $lastIndex = $this->Room->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'building_id' => $buildingID,
            'room_id' => $lastIndex,
            'message' => "User " . $request->getAttribute('email') . " created new room in building id=$buildingID"
        ]);
        return $response->withStatus(204, "Successfully created");
    }

    public function updateRoomByID(Request $request, Response $response, $args): Response
    {
        /**
         * creating room in specified building
         * POST /buildings/{building_id}/rooms
         * {
         *      "name":"",
         *      "room_type_id":1,
         *      "seats_count":1,
         *      "floor":1,
         *      "equipment":"umywalka,kreda,tablica",
         *      "blockade":true,
         *      "status":false
         * }
         * @param Request $request 
         * @param Response $response
         * @param array $args
         * 
         * @return Response 
         */
        $roomID = (int) $args['room_id'];
        $buildingID = (int) $args['building_id'];

        $data = $this->getFrom($request);

        $this->Room->update($roomID, $data);

        $dataString = implode(',', array_keys($data));
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $roomID,
            'building_id' => $buildingID,
            'message' => "User " . $request->getAttribute('email') . " updated room data: $dataString"
        ]);

        return $response->withStatus(204, "Successfully updated");
    }

    public function deleteRoomByID(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting room from building
         * DELETE /building/{building_id}/rooms/{room_id}
         * 
         */
        $roomID = (int) $args['room_id'];
        $buildingID = (int) $args['building_id'];

        $this->Room->delete($roomID);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $roomID,
            'building_id' => $buildingID,
            'message' => "User " . $request->getAttribute('email') . " deleted room"
        ]);
        return $response->withStatus(204, "Successfully deleted");
    }
}
