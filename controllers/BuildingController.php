<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";

class BuildingController extends Controller
{
    private $Address; // relation; building addres
    protected $DIcontainer;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Building = $this->DIcontainer->get('Building');
    }

    public function getAllBuildings(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Building controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchBuildings(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Building controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getBuildingByID(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Building controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createNewBuilding(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Building controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateBuilding(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Building controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteBuilding(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Building controller");
        return $response->withHeader('Content-Type', 'application/json');
    }
}
