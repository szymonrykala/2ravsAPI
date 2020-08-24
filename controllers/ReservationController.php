<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . "/Controller.php";

class ReservationController extends Controller
{
    private $Reservation;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Reservation = $this->DIcontainer->get('Reservation');
    }

    // ?ext=user_id,building_id,room_id...
    public function handleExtensions(array $reservations, $extensions): array
    {
        if (in_array('room_id', $extensions)) {
            $Room = $this->DIcontainer->get('Room');
        }
        if (in_array('building_id', $extensions)) {
            $Building = $this->DIcontainer->get('Building');
        }
        if (in_array('user_id', $extensions) || in_array('confirming_user_id', $extensions)) {
            $User = $this->DIcontainer->get('User');
        }


        foreach ($reservations as &$reservation) {
            if (in_array('room_id', $extensions)) {
                $reservation['room'] = $Room->read(['id' => $reservation['room_id']])[0];
                unset($reservation['room_id']);
            }
            if (in_array('building_id', $extensions)) {
                $reservation['building'] = $Building->read(['id' => $reservation['building_id']])[0];
                unset($reservation['building_id']);
            }
            if (in_array('user_id', $extensions)) {
                $reservation['user'] = $User->read(['id' => $reservation['user_id']])[0];
                unset($reservation['user_id']);
            }
            if (in_array('confirming_user_id', $extensions) && !empty($reservation['confirming_user_id'])) {
                $reservation['confirming_user'] = $User->read(['id' => $reservation['confirming_user_id']])[0];
                unset($reservation['confirming_user_id']);
            }
        }
        return $reservations;
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
        $extensions = $this->getQueryParam($request, 'ext');
        
        $data = $this->Reservation->read(['user_id' => $args['userID']]);
        $data = $this->handleExtensions($data,$extensions);

        $response->getBody()->write(json_encode($data));
        return $response;
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
