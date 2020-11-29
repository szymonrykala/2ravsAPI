<?php

namespace controllers;

use Exception;
use models\GenericModel;
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
    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Model = $this->DIcontainer->get(Room::class);
    }


    /**
     * Toggle the state of room with rfid in "rfid"
     * PATCH /buildings/rooms/rfid/{rfid}
     * 
     * @param Request $request 
     * @param Response $response
     * @param array $args
     * 
     * @return Response
     */
    public function toggleOccupied(Request $request, Response $response, $args): Response
    {
        $this->Model->data = $this->Model->read($args)[0];

        $this->Model->data['occupied'] = !$this->Model->data['occupied']; //toggle occupation of the room

        $this->Model->update(['occupied' => $this->Model->data['occupied']]);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $this->Model->data['id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE room DATA ' . json_encode(['occupied' => $this->Model->data['occupied']])
        ]);

        $response->getBody()->write('Room toggled to ' . ($this->Model->data['occupied'] ? 'true' : 'false'));
        return $response->withStatus(200);
    }


    /**
     * Getting all rooms or all rooms in building
     * GET /buildings/rooms
     * GET /buildings/rooms/{room_id}
     * GET /buildings/{building_id}/rooms
     * GET /buildings/{building_id}/rooms/{room_id}
     * 
     * @param Request $request 
     * @param Response $response
     * @param array $args
     * 
     * @return Response 
     */
    public function getRooms(Request $request, Response $response, $args): Response
    {
        $this->switchKey($args, 'room_id', 'id');
        return parent::get($request, $response, $args);
    }


    /**
     * Getting room by rfid code
     * GET /buildings/rooms/rfid/{rfid}
     * 
     * @param Request $request 
     * @param Response $response
     * @param array $args
     * 
     * @return Response
     */
    public function getRoomByRFID(Request $request, Response $response, $args): Response
    {
        $data = $this->handleExtensions($this->Model->read($args), $request);

        $response->getBody()->write(json_encode($data));

        return $response->withStatus(200);
    }


    /**
     * creating room in specified building
     * returning 201
     * POST /buildings/{building_id}/rooms
     * 
     * @param Request $request 
     * @param Response $response
     * @param array $args
     * 
     * @return Response 
     */
    public function createRoom(Request $request, Response $response, $args): Response
    {
        $data = $this->getParsedData($request);
        $data['blockade'] = $this->DIcontainer->get('settings')['default_params']['room_blockade'];

        $data['building_id'] = (int) $args['building_id'];
        if (!isset($data['rfid'])) $data['rfid'] = random_bytes(10);

        $lastIndex = $this->Model->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'building_id' => $args['building_id'],
            'room_id' => $lastIndex,
            'message' => "USER " . $request->getAttribute('email') . " CREATE room DATA " . json_encode($data)
        ]);
        return $response->withStatus(201, "Created");
    }


    /**
     * creating room in specified building
     * returning 204
     * PATCH /buildings/rooms/{room_id}
     * @param Request $request 
     * @param Response $response
     * @param array $args
     * 
     * @return Response 
     */
    public function updateRoom(Request $request, Response $response, $args): Response
    {
        $data = $this->getParsedData($request);

        $this->Model->data = $this->Model->read(['id' => $args['room_id']])[0];
        $this->Model->update($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $args['room_id'],
            'message' => "USER " . $request->getAttribute('email') . " UPDATE room DATA " . json_encode($data)
        ]);

        return $response->withStatus(204, "Updated");
    }


    /**
     * Deleting room from building
     * returning 204
     * DELETE /building/rooms/{room_id}
     * @param Request $request 
     * @param Response $response
     * @param array $args
     * 
     * @return Response 
     */
    public function deleteRoom(Request $request, Response $response, $args): Response
    {
        $this->Model->data = $this->Model->read(['id' => $args['room_id']])[0];
        $this->Model->delete();

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'room_id' => $args['room_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE room DATA ' . json_encode($this->Model->data)
        ]);
        return $response->withStatus(204, "Deleted");
    }
}
