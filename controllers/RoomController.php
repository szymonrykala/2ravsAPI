<?php

namespace controllers;

use models\HttpConflictException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use models\Room;
use Slim\Exception\HttpNotFoundException;
use utils\Validator;

class RoomController extends Controller
{
    /**
     * Responsible for operation with /rooms table in database
     */
    private Room $Room;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Room = $this->DIcontainer->get(Room::class);
    }

    public function validateRoom(Request $request, array &$data): void
    {
        /**
         * Validate Room
         * 
         * @param array $data
         * @throws HttpBadRequestException
         */
        $Validator = $this->DIcontainer->get(Validator::class);
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

    // PATCH /rfid
    public function rfidAction(Request $request, Response $response, $args): Response
    {
        /**
         * Toggle the state of room with rfid in "rfid"
         * {
         *      "rfid" : ""
         * }
         */
        $rfid = $this->getFrom($request, ['rfid' => 'string'], true)['rfid'];
        $rfid = str_replace(' ', '', $rfid);
        if (empty($rfid)) {
            throw new HttpBadRequestException($request, 'Bad variable value - `rfid` can not be empty');
        }

        $room = $this->Room->read(['rfid' => $rfid])[0];

        $this->Room->update($room['id'], ['state' => (bool)!$room['state']]);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $room['id'],
            'message' => 'User ' . $request->getAttribute('email') . ' toggled to ' . (!$room['state'] ? 'true' : 'false') . ' state of room with rfid: ' . $rfid
        ]);

        $response->getBody()->write('toggled to ' . (!$room['state'] ? 'true' : 'false'));
        return $response->withStatus(200);
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
         *      "rfid":"sdafgw435tgwtr",
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
            'rfid' => 'string',
            'equipment' => 'string'
        ], true);
        $data['blockade'] = $this->DIcontainer->get('settings')['default_params']['room_blockade'];
        $this->validateRoom($request, $data);

        if ($this->Room->exist(['rfid' => $data['rfid']])) throw new HttpConflictException("Room with given `rfid` already exist. RFID identificator have to be unique");

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

    // PATCH /buildings/rooms/{room_id}
    public function updateRoom(Request $request, Response $response, $args): Response
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
         *      "blockade":true
         * }
         * @param Request $request 
         * @param Response $response
         * @param array $args
         * 
         * @return Response 
         */

        $data = $this->getFrom($request, [
            'name' => "string",
            'room_type_id' => 'integer',
            'seats_count' => 'integer',
            'floor' => 'integer',
            'rfid' => 'string',
            'blockade' => 'boolean',
            'equipment' => 'string'
        ], false);

        $this->validateRoom($request, $data);

        if ($this->Room->exist(['rfid' => $data['rfid']])) throw new HttpConflictException("Room with given `rfid` already exist. RFID identificator have to be unique");
        $this->Room->update($args['room_id'], $data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $args['room_id'],
            'message' => "User " . $request->getAttribute('email') . " updated room data:" . json_encode($data)
        ]);

        return $response->withStatus(204, "Updated");
    }

    // DELETE /building/rooms/{room_id}
    public function deleteRoom(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting room from building
         * returning 204
         * DELETE /building/rooms/{room_id}
         * 
         */

        $this->Room->delete($args['room_id']);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $args['room_id'],
            'message' => "User " . $request->getAttribute('email') . " deleted room"
        ]);
        return $response->withStatus(204, "Deleted");
    }
}
