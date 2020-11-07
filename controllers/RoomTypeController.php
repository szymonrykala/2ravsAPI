<?php

namespace controllers;

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
    private RoomType $Type;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Type = $this->DIcontainer->get(RoomType::class);
    }

    // GET /buildings/rooms/types
    public function getTypes(Request $request, Response $response, $args): Response
    {
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
        ['params' => $params, 'mode' => $mode] = $this->getSearchParams($request);
        if (isset($params) && isset($mode))  $this->Type->setSearch($mode, $params);

        $this->Type->setQueryStringParams($this->parsedQueryString($request));

        $this->switchKey($args, 'room_type_id', 'id');
        $data = $this->Type->read($args);
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // POST /buildings/rooms/types
    public function createType(Request $request, Response $response, $args): Response
    {
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

        $data = $this->getParsedData($request);
        $data['id'] = $this->Type->create($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => "USER " . $request->getAttribute('email') . " CREATE room_type DATA " . json_encode($data)
        ]);
        return $response->withStatus(201, "Created");
    }

    // PATCH /buildings/rooms/types/{room_type_id}
    public function updateType(Request $request, Response $response, $args): Response
    {
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
        $data = $this->getParsedData($request);

        $this->Type->data = $this->Type->read(['id' => $args['room_type_id']]);
        $this->Type->update($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE room_type DATA ' . json_encode($data)
        ]);
        return $response->withStatus(204, "Updated");
    }

    // DELETE /buildings/rooms/types/{room_type_id}
    public function deleteType(Request $request, Response $response, $args): Response
    {
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
        $this->Type->data = $this->Type->read(['id' => $args['room_type_id']])[0];
        $this->Type->delete();

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE room_type DATA ' . json_encode($this->Type->data)
        ]);
        return $response->withStatus(204, 'Deleted');
    }
}
