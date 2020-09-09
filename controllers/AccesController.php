<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";

class AccesController extends Controller
{
    private $Acces;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Acces = $DIcontainer->get('Acces');
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
