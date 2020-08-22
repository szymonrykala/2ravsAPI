<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddressController
{

    private $Address;
    private $View;
    protected $DIContainer;


    public function __construct(ContainerInterface $DIContainer)
    {
        $this->DIContainer = $DIContainer;
        $this->Address = $this->DIContainer->get('Address');
        $this->View = $this->DIContainer->get('View');
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
