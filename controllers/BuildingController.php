<?php

namespace controllers;

use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use models\Building;
use models\Address;

class BuildingController extends Controller
{
    /**
     * Implement endpoints related with buildings paths
     * 
     */
    protected Building $Building;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Building = $this->DIcontainer->get(Building::class);
    }

    // GET /buildings | {id}
    public function getBuildings(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all buildings from database
         * returning array of buildings
         * GET /buildings
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        ['params' => $params, 'mode' => $mode] = $this->getSearchParams($request);
        if (isset($params) && isset($mode))  $this->Building->setSearch($mode, $params);

        $this->Building->setQueryStringParams($this->parsedQueryString($request));

        $this->switchKey($args, 'building_id', 'id');
        $data = $this->handleExtensions($this->Building->read($args), $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // POST /buildings
    public function createBuilding(Request $request, Response $response, $args): Response
    {
        /**
         * Creating new Building with data from request body
         * POST /buildings
         * {
         *      "name":"",
         *      "rooms_count":20,
         *      "address_id":2
         * }
         * 
         * @param Request $request 
         * @param Response $response 
         * @param array $args
         * 
         * @return Response 
         */
        $data = $this->getParsedData($request);
        $data['id'] = $this->Building->create($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'building_id' => $data['id'],
            'message' => "USER " . $request->getAttribute('email') . " CREATE building DATA " . json_encode($data)
        ]);
        return $response->withStatus(201, "Created");
    }

    // PATCH /buildings/{building_id}
    public function updateBuilding(Request $request, Response $response, $args): Response
    {
        /**
         * Updating sepcific BUilding with data from request body
         * PATCH /building/{building_id}
         * {
         *      "name":"",
         *      "rooms_count":20,
         *      "address_id":2
         * }
         * 
         * @param Request $request 
         * @param Response $response 
         * @param array $args
         * 
         * @return Response 
         */
        $this->Building->data = $this->Building->read(['id' => $args['building_id']])[0];

        $data = $this->getParsedData($request);

        $this->Building->update($data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'building_id' => $args['building_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE building DATA ' . json_encode($data)
        ]);
        return $response->withStatus(204, "Updated");
    }

    // DELETE /buildings/{building_id}
    public function deleteBuilding(Request $request, Response $response, $args): Response
    {
        /**
         * Deleting specific building by building_id
         * DELETE /buildings/{building_id}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param array $args
         * 
         * @return Response 
         */
        $this->Building->data = $this->Building->read(['id' => $args['building_id']])[0];
        $this->Building->delete();

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'building_id' => $args['building_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE building DATA ' . json_encode($this->Building->data)
        ]);

        return $response->withStatus(204, "Deleted");
    }
}
