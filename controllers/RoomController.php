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

    // PATCH /buildings/rooms/rfid/{rfid}
    public function toggleOccupied(Request $request, Response $response, $args): Response
    {
        /**
         * Toggle the state of room with rfid in "rfid"
         */
        $this->Room->data = $this->Room->read($args)[0];

        $this->Room->data['occupied'] = !$this->Room->data['occupied']; //toggle occupation of the room

        $this->Room->update(['occupied' => $this->Room->data['occupied']]);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $this->Room->data['id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE room DATA ' . json_encode(['occupied' => $this->Room->data['occupied']])
        ]);

        $response->getBody()->write('Room toggled to ' . ($this->Room->data['occupied'] ? 'true' : 'false'));
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

    // GET /buildings/rooms/rfid/{rfid}
    public function getRoomByRFID(Request $request, Response $response, $args): Response
    {
        /**
         * Getting room by rfid code
         */
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

        $this->Room->data = $this->Room->read(['id' => $args['room_id']])[0];
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

        $this->Room->data = $this->Room->read(['id' => $args['room_id']])[0];
        $this->Room->delete();

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $args['room_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE room DATA ' . json_encode($this->Room->data)
        ]);
        return $response->withStatus(204, "Deleted");
    }
}
