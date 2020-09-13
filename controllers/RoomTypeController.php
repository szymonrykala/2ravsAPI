<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/Controller.php";

class RoomTypeController extends Controller
{
    /**
     * Implement endpoints related with buildings/rooms/types paths
     * 
     */
    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Building = $this->DIcontainer->get('Building');
    }

    public function getAllTypes(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write('');
        return $response;
    }
    
    public function createType(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write('');
        return $response;
    }
    
    public function updateType(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write('');
        return $response;
    }

    public function deleteType(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write('');
        return $response;
    }
}
