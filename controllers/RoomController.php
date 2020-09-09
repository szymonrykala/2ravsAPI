<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\InvalidArgumentException;

require_once __DIR__ . "/Controller.php";

class RoomController extends Controller
{
    protected $DIcontainer;
    private $View;
    private $Room;
    private $Building;
    private $RoomType;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Room = $this->DIcontainer->get('Room');
        $this->RoomType = $this->DIcontainer->get('RoomType');
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
