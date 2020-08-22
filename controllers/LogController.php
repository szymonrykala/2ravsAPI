<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LogController
{
    protected $DIcontainer;
    private $View;
    private $Logs;

    public function __construct(ContainerInterface $DIcontainer)
    {
        $this->DIcontainer = $DIcontainer;
        $this->Logs = $this->DIcontainer->get('Log');
        $this->View = $this->DIcontainer->get('View');
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
