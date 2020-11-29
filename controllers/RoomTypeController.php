<?php

namespace controllers;

use models\GenericModel;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use models\RoomType;

class RoomTypeController extends Controller
{
    /**
     * Implement endpoints related with buildings/rooms/types paths
     * 
     */
    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Model = $this->DIcontainer->get(RoomType::class);
    }


    /**
     * Getting all room types,
     * returning array of items
     * GET /buildings/rooms/types
     * 
     * @param Request $request
     * @param Response $response
     * @param array $array
     * 
     * @return Response $response
     */
    public function getTypes(Request $request, Response $response, $args): Response
    {
        $this->switchKey($args, 'room_type_id', 'id');
        return parent::get($request, $response, $args);
    }


    /**
     * Creating new room type,
     * POST /buildings/rooms/types
     * 
     * @param Request $request
     * @param Response $response
     * @param array $array
     * 
     * @return Response $response
     */
    public function createType(Request $request, Response $response, $args): Response
    {
        $data = $this->getParsedData($request);
        $data['id'] = $this->Model->create($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' CREATE room_type DATA ' . json_encode($data)
        ]);
        return $response->withStatus(201, "Created");
    }

    /**
     * Updating room type by room_type_id,
     * PATCH /buildings/rooms/types/{room_type_id}
     * 
     * @param Request $request
     * @param Response $response
     * @param array $array
     * 
     * @return Response $response
     */
    public function updateType(Request $request, Response $response, $args): Response
    {
        $data = $this->getParsedData($request);

        $this->Model->data = $this->Model->read(['id' => $args['room_type_id']])[0];
        $this->Model->update($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE room_type DATA ' . json_encode($data)
        ]);
        return $response->withStatus(204, "Updated");
    }

    /**
     * Deleting room type by room_type_id,
     * DELETE /buildings/rooms/types/{room_type_id}
     * 
     * @param Request $request
     * @param Response $response
     * @param array $array
     * 
     * @return Response $response
     */
    public function deleteType(Request $request, Response $response, $args): Response
    {
        $this->Model->data = $this->Model->read(['id' => $args['room_type_id']])[0];
        $this->Model->delete();

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE room_type DATA ' . json_encode($this->Model->data)
        ]);
        return $response->withStatus(204, 'Deleted');
    }
}
