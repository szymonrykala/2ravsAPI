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
    private function handleExtensions(array $reservations, Request $request): array
    {
        $extensions = $this->getQueryParam($request, 'ext');

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
                unset($reservation['user']['password']);
                unset($reservation['user']['action_key']);
                unset($reservation['user']['login_fails']);
            }
            if (in_array('confirming_user_id', $extensions) && $reservation['confirmed']) {
                $reservation['confirming_user'] = $User->read(['id' => $reservation['confirming_user_id']])[0];
                unset($reservation['confirming_user_id']);
                unset($reservation['confirming_user']['password']);
                unset($reservation['confirming_user']['action_key']);
                unset($reservation['confirming_user']['login_fails']);
            } else {
                $reservation['confirming_user_id'] = null;
            }
        }
        return $reservations;
    }

    // GET /reservations
    public function getAllReservations(Request $request, Response $response, $args): Response
    {
        $data = $this->Reservation->read();
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response;
    }


    public function confirmReservation(Request $request, Response $response, $args): Response
    {
        $response->getBody()->write("Middleware");
        return $response->withHeader('Content-Type', 'application/json');
    }

    // GET /reservations/{reservation_id}
    public function getReservationByID(Request $request, Response $response, $args): Response
    {
        $data = $this->Reservation->read(['id' => $args['reservation_id']]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET /users/{userID}/reservations
    public function getUserReservations(Request $request, Response $response, $args): Response
    {
        $data = $this->Reservation->read(['user_id' => $args['userID']]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET building/{building_id}/reservations
    public function getReservationsInBuilding(Request $request, Response $response, $args): Response
    {
        $data = $this->Reservation->read(['building_id' => $args['building_id']]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET building/{building_id}/rooms/{room_id}/reservations
    public function getRoomReservations(Request $request, Response $response, $args): Response
    {
        $data = $this->Reservation->read(['building_id' => $args['building_id'], 'room_id' => $args['room_id']]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // POST /buildings/{building_id}/rooms/{room_id}/reservations
    /* {
        "title":"rezerwacja v1",
        "subtitle":"podtytuł rezerwacji, opis",
        "start_time":"10:00",
        "end_time":"11:15",
        "date":"2020-08-28"
    } */
    public function createReservation(Request $request, Response $response, $args): Response
    {
        $currentUser = $request->getAttribute('user_id');
        $currentUserMail = $request->getAttribute('email');
        $buildingID = (int)$args['building_id'];
        $roomID = (int)$args['room_id'];

        list(
            "title" => $title,
            "subtitle" => $subtitle,
            "start_time" => $startTime,
            "end_time" => $endTime,
            "date" => $date
        ) = $this->getFrom($request, [
            "title" => 'string',
            "subtitle" => "string",
            "start_time" => "string",
            "end_time" => "string",
            "date" => "string"
        ]);

        $reservationID = $this->Reservation->create([
            "title" => $title,
            "subtitle" => $subtitle,
            "start_time" => $startTime,
            "end_time" => $endTime,
            "date" => $date,
            "room_id" => $roomID,
            "building_id" => $buildingID,
            "user_id" => $currentUser
        ]);
        $this->Log->create([
            'user_id' => $currentUser,
            'reservation_id' => $reservationID,
            'room_id' => $roomID,
            'building_id' => $buildingID,
            'message' => "User $currentUserMail created reservation"
        ]);

        return $response->withStatus(201, "Reservation created");
    }

    // PATCH /reservations/{reservation_id}
    /* {
        "title":"rezerwacja v1",
        "subtitle":"podtytuł rezerwacji, opis",
        "start_time":"10:00",
        "end_time":"11:15",
        "date":"2020-08-28"
    } */
    public function updateReservationByID(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $reservationID = $args['reservation_id'];
        $currentUser = $request->getAttribute('user_id');
        $currentUserMail = $request->getAttribute('email');

        $this->Reservation->update($reservationID, $data);
        $this->Log->create([
            'user_id' => $currentUser,
            'reservation_id' => $reservationID,
            'message' => "User $currentUserMail deleted reservation id=$reservationID"
        ]);
        return $response->withStatus(200, "Reservation updated");
    }

    // DELETE /reservations/{reservation_id}
    public function deleteReservationsByID(Request $request, Response $response, $args): Response
    {
        $currentUser = $request->getAttribute('user_id');
        $currentUserMail = $request->getAttribute('email');
        $reservationID = (int)$args['reservation_id'];

        $this->Reservation->delete((int) $args['reservation_id']);
        
        $this->Log->create([
            'user_id' => (int)$currentUser,
            'reservation_id' => $reservationID,
            'message' => "User $currentUserMail deleted reservation with id=$reservationID"
        ]);

        return $response->withStatus(200);
    }
}
