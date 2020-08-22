<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\InvalidArgumentException;

class RoomController
{
    protected $DIcontainer;
    private $View;
    private $Room;
    private $Building;
    private $RoomType;

    public function __construct(ContainerInterface $DIcontainer)
    {
        $this->DIcontainer = $DIcontainer;
        $this->Room = $this->DIcontainer->get('Room');
        $this->Building = $this->DIcontainer->get('Building');
        $this->RoomType = $this->DIcontainer->get('RoomType');
        $this->View = $this->DIcontainer->get('View');
    }

    public function getAllRoomsInBuilding(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Room controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getRoomByID(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Room controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createRoom(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Room controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateRoomByID(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Room controller");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteRoomByID(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Room controller");
        return $response->withHeader('Content-Type', 'application/json');
    }
}
