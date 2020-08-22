<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class AccesController
{
    private $Acces = null;
    public $View = null;

    public function __construct(ContainerInterface $DIcontainer)
    {
        $this->DIcontainer = $DIcontainer;
        $this->Acces = $DIcontainer->get('Acces');
        $this->View = $DIcontainer->get('View');
    }

    public function createNewAccesType(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Access");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAllAccesTypes(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Access");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAccesTypeByID(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Access");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateAccesType(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Access");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteAccesType(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Access");
        return $response->withHeader('Content-Type', 'application/json');
    }
}
