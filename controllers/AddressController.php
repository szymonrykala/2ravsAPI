<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";

class AddressController extends Controller
{
    private $Address;
    protected $DIContainer;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Address = $this->DIcontainer->get('Address');
    }

    public function getAddress(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Address");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAllAddresses(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Address");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createAddress(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Address");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteAddress(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Address");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateAddress(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Address");
        return $response->withHeader('Content-Type', 'application/json');
    }
}
