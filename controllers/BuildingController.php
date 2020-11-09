<?php

namespace controllers;

use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use models\Building;
use models\Address;
use models\GenericModel;

class BuildingController extends Controller
{
    /**
     * Implement endpoints related with buildings paths
     * 
     */
    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Model = $this->DIcontainer->get(Building::class);
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
        $this->switchKey($args, 'building_id', 'id');
        return parent::get($request,$response,$args);
    }

    // POST /buildings
    public function createBuilding(Request $request, Response $response, $args): Response
    {
        /**
         * Creating new Building with data from request body
         * POST /buildings
         * 
         * @param Request $request 
         * @param Response $response 
         * @param array $args
         * 
         * @return Response 
         */
        $data = $this->getParsedData($request);
        $data['id'] = $this->Model->create($data);

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
         * 
         * @param Request $request 
         * @param Response $response 
         * @param array $args
         * 
         * @return Response 
         */
        $this->Model->data = $this->Model->read(['id' => $args['building_id']])[0];

        $data = $this->getParsedData($request);

        $this->Model->update($data);

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
        $this->Model->data = $this->Model->read(['id' => $args['building_id']])[0];
        $this->Model->delete();

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'building_id' => $args['building_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE building DATA ' . json_encode($this->Model->data)
        ]);

        return $response->withStatus(204, "Deleted");
    }
}
