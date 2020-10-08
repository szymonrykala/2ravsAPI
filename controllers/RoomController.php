<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

require_once __DIR__ . "/Controller.php";

class RoomController extends Controller
{
    /**
     * Responsible for operation with /rooms table in database
     */
    private $Room = null;
    private $request = null;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Room = $this->DIcontainer->get('Room');
    }

    public function validateRoom(Request $request, array &$data): void
    {
        /**
         * Validate Room
         * 
         * @param array $data
         * @throws HttpBadRequestException
         */
        $Validator = $this->DIcontainer->get('Validator');
        if (isset($data['name'])) {
            if (!$Validator->validateClearString($data['name'])) {
                throw new HttpBadRequestException($request, 'Incorrect room name value; pattern: ' . $Validator->clearString);
            }
        }

        if (isset($data['equipment'])) {
            if (!$Validator->validateString($data['equipment'], 1)) {
                throw new HttpBadRequestException($request, 'Incorrect room equipment value (min 1 char. length).');
            }
            $data['equipment'] = $Validator->sanitizeString($data['equipment']);
        }
    }

    // GET /buildings/rooms
    // GET /buildings/rooms/{room_id}
    // GET /buildings/{building_id}/rooms
    // GET /buildings/{building_id}/rooms/{room_id}
    public function getRooms(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all rooms or all rooms in building
         * GET /buildings/rooms
         * GET /buildings/{building_id}/rooms
         * 
         * @param Request $request 
         * @param Response $response
         * @param array $args
         * 
         * @return Response 
         */
        ['params' => $params, 'mode' => $mode] = $this->getSearchParams($request);
        if (isset($params) && isset($mode))  $this->Room->setSearch($mode, $params);

        $this->Room->setQueryStringParams($this->parsedQueryString($request));

        $this->switchKey($args, 'room_id', 'id');
        $data = $this->handleExtensions($this->Room->read($args), $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // POST /buildings/{building_id}/rooms
    public function createRoom(Request $request, Response $response, $args): Response
    {
        /**
         * creating room in specified building
         * returning 201
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
        ], true);

        $this->validateRoom($request, $data);

        $data['building_id'] = $buildingID;
        $lastIndex = $this->Room->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'building_id' => $buildingID,
            'room_id' => $lastIndex,
            'message' => "User " . $request->getAttribute('email') . " created new room in building id=$buildingID; data:" . json_encode($data)
        ]);
        return $response->withStatus(201, "Created");
    }

    // PATCH /buildings/{building_id}/rooms/{room_id}
    public function updateRoomByID(Request $request, Response $response, $args): Response
    {
        /**
         * creating room in specified building
         * returning 204
         * PATCH /buildings/{building_id}/rooms/{room_id}
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

        $data = $this->getFrom($request, [
            'name' => "string",
            'room_type_id' => 'integer',
            'seats_count' => 'integer',
            'floor' => 'integer',
            'equipment' => 'string'
        ], false);

        $this->validateRoom($request, $data);

        $this->Room->update($roomID, $data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $roomID,
            'building_id' => $buildingID,
            'message' => "User " . $request->getAttribute('email') . " updated room data:" . json_encode($data)
        ]);

        return $response->withStatus(204, "Updated");
    }

    // DELETE /building/{building_id}/rooms/{room_id}
    public function deleteRoomByID(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting room from building
         * returning 204
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
        return $response->withStatus(204, "Deleted");
    }
}
