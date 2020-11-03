<?php

namespace controllers;

use models\HttpConflictException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use models\Room;
use Slim\Exception\HttpNotFoundException;

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

    // PATCH /rfid
    public function rfidAction(Request $request, Response $response, $args): Response
    {
        /**
         * Toggle the state of room with rfid in "rfid"
         * {
         *      "rfid" : ""
         * }
         */
        $rfid = $this->getParsedData($request)['rfid'];
        $rfid = str_replace(' ', '', $rfid);
        if (empty($rfid)) {
            throw new HttpBadRequestException($request, 'Bad variable value - `rfid` can not be empty');
        }

        $room = $this->Room->read(['rfid' => $rfid])[0];

        $this->Room->setID($room['id']);
        $this->Room->update(['state' => (bool)!$room['state']]);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $room['id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE room DATA ' . json_encode(['state' => !$room['state']])
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

        $data = $this->getParsedData($request);
        $data['blockade'] = $this->DIcontainer->get('settings')['default_params']['room_blockade'];

        $data['building_id'] = (int) $args['building_id'];
        $lastIndex = $this->Room->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'building_id' => $args['building_id'],
            'room_id' => $lastIndex,
            'message' => "USER " . $request->getAttribute('email') . " CREATE room DATA " . json_encode($data)
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

        $data = $this->getParsedData($request);

        $this->Room->setID($args['room_id']);
        $this->Room->update($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $args['room_id'],
            'message' => "USER " . $request->getAttribute('email') . " UPDATE room DATA " . json_encode($data)
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

        $room = $this->Room->read(['id' => $args['room_id']])[0];

        $this->Room->setID($args['room_id']);
        $this->Room->delete($args['room_id']);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $args['room_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE room DATA ' . json_encode($room)
        ]);
        return $response->withStatus(204, "Deleted");
    }
}
