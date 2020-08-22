<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ReservationController
{
    protected $DIcontainer;
    private $Reservation;
    private $response;
    private $User;
    private $Building;
    private $Room;

    public function __construct(ContainerInterface $DIcontainer)
    {
        $this->DIcontainer = $DIcontainer;
        $this->Reservation = $this->DIcontainer->get('Reservation');
        $this->User = $this->DIcontainer->get('User');
        $this->Building = $this->DIcontainer->get('Building');
        $this->Room = $this->DIcontainer->get('Room');
        $this->View = $this->DIcontainer->get('View');
        $this->Acces = $this->DIcontainer->get('Acces');
        $this->Logs = $this->DIcontainer->get('Log');
    }

    public function getAllReservations(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Middleware");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function confirmReservation(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Middleware");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getReservationByID(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Middleware");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUserReservations(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Middleware");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getReservationsInBuilding(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Middleware");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createReservation(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Middleware");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateReservationByID(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Middleware");
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteReservationsByID(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Middleware");
        return $response->withHeader('Content-Type', 'application/json');
    }
}
