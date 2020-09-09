<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . "/Controller.php";

class LogController extends Controller
{
    protected $DIcontainer;
    private $Log;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Log = $this->DIcontainer->get('Reservation');
    }

    public function getAllLogs(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Logs controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteLogByID(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Logs controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchLogs(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Logs controller");
        return $response->withHeader('Content-Type', 'application/json');
    }
}
