<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

require_once __DIR__ . "/Controller.php";

class BuildingController extends Controller
{
    /**
     * Implement endpoints related with buildings paths
     * 
     */
    protected $Building;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Building = $this->DIcontainer->get('Building');
    }

    // GET /buildings
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
        if (isset($args['building_id'])) {
            $args['id'] = $args['building_id'];
            unset($args['building_id']);
        }
        $data = $this->handleExtensions($this->Building->read($args), $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // GET /buildings/search
    public function searchBuildings(Request $request, Response $response, $args): Response
    {
        /**
         * Searching for Building with parameters given in Request(query string or body['search'])
         * Founded results are written into the response body
         * GET /buildings/search?<queryString>
         * { "search":{"key":"value","key2":"value2"}}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param array $args
         * 
         * @return Response 
         */
        $params = $this->getSearchParams($request);

        $data = $this->Building->search($params);;

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
        $data = $this->getFrom($request, ['name' => 'string', 'rooms_count' => 'integer', 'address_id' => 'integer']);

        $Address = $this->DIcontainer->get("Address");
        if (!$Address->exist(['id' => $data['address_id']])) {
            throw new HttpBadRequestException($request, "Address with id=" . $data['address_id'] . " do not exist. You cannot create building with data:" . json_encode($data));
        }

        $userMail = $request->getAttribute('email');
        $userID = $request->getAttribute('user_id');

        $lastIndex = $this->Building->create($data);
        $this->Log->create([
            'user_id' => $userID,
            'building_id' => $lastIndex,
            'message' => "User $userMail created Building id=$lastIndex; data:" . json_encode($data)
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
        $data = $this->getFrom($request);
        $userMail = $request->getAttribute('email');
        $userID = $request->getAttribute('user_id');
        $buildingID = (int)$args['building_id'];


        $this->Building->update($buildingID, $data);

        $this->Log->create([
            'user_id' => $userID,
            'building_id' => $buildingID,
            'message' => "user $userMail updated Building id=$buildingID data:" . json_encode($data)
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
        $userMail = $request->getAttribute('email');
        $userID = $request->getAttribute('user_id');
        $buildingID = (int)$args['building_id'];

        $this->Building->delete((int)$args['building_id']);
        $this->Log->create([
            'user_id' => $userID,
            'building_id' => $buildingID,
            'message' => "User $userMail deleted Building id=$buildingID"
        ]);

        return $response->withStatus(204, "Deleted");
    }
}
