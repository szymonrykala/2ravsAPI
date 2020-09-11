<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";

class BuildingController extends Controller
{
    /**
     * Implement endpoints related with buildings paths
     * 
     */
    private $Address; // relation; building addres
    protected $DIcontainer;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Building = $this->DIcontainer->get('Building');
    }

    // GET /buildings/{building_id}
    public function getAllBuildings(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all buildings
         * /buildings/{building_id}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $data = $this->handleExtensions($this->Building->read(), $request);
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET /buildings/search
    public function searchBuildings(Request $request, Response $response, $args): Response
    {
        /**
         * Searching for Building with parameters given in Request(query string or body['search'])
         * Founded results are written into the response body
         * /buildings/search?<queryString>
         * { "search":{"key":"value","key2":"value2"}}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $params = $this->getSearchParams($request);

        $data = $this->Building->search($params);;

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // GET /buildings/{building_id}
    public function getBuildingByID(Request $request, Response $response, $args): Response
    {
        /**
         * Getting building by building_id
         * /buildings/{building_id}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $Address = $this->DIcontainer->get('Address');

        $data = $this->Building->read(['id' => (int)$args['building_id']])[0];
        $data['address'] = $Address->read(['id' => $data['address_id']])[0];
        unset($data['address_id']);
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // POST /buildings
    public function createNewBuilding(Request $request, Response $response, $args): Response
    {
        /**
         * Creating new Building with data from request body
         * {
         *      "name":"",
         *      "rooms_count":20,
         *      "address_id":2
         * }
         * /buildings
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $data = $this->getFrom($request, ['name' => 'string', 'rooms_count' => 'integer', 'address_id' => 'integer']);
        $userMail = $request->getAttribute('email');
        $userID = $request->getAttribute('user_id');

        $lastIndex = $this->Building->create($data);
        $this->Log->create([
            'user_id' => $userID,
            'building_id' => $lastIndex,
            'message' => "User $userMail created Building id=$lastIndex"
        ]);
        return $response->withStatus(204, "Succesfully created");
    }

    // PATCH /buildings/{building_id}
    public function updateBuilding(Request $request, Response $response, $args): Response
    {
        /**
         * Updating sepcific BUilding with data from request body
         * /building/{building_id}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
        */
        $data = $this->getFrom($request);
        $userMail = $request->getAttribute('email');
        $userID = $request->getAttribute('user_id');
        $buildingID = (int)$args['building_id'];


        $this->Building->update($buildingID, $data);

        $dataString = implode(',', array_keys($data));
        $this->Log->create([
            'user_id' => $userID,
            'building_id' => $buildingID,
            'message' => "user $userMail updated Building id=$buildingID data: $dataString"
        ]);
        return $response->withStatus(204, "Succesfully updated");
    }

    // DELETE /buildings/{building_id}
    public function deleteBuilding(Request $request, Response $response, $args): Response
    {
        /* dont work becouse of db integrity */
        /**
         * Deleting specific building by building_id
         * /buildings
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
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

        return $response->withStatus(204, "Succesfully deleted");
    }
}
