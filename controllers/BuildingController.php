<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BuildingController
{
    private $Building;
    private $Address; // relation; building addres
    private $View;
    protected $DIcontainer;

    public function __construct(ContainerInterface $DIcontainer)
    {
        $this->DIcontainer = $DIcontainer;
        $this->Building = $this->DIcontainer->get('Building');
        $this->Address = $this->DIcontainer->get('Address');
        $this->View = $this->DIcontainer->get('View');
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
