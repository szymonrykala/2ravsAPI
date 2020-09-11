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

    // GET /reservations
    public function getAllReservations(Request $request, Response $response, $args): Response
    {
        $data = $this->Reservation->read(['deleted' => $this->deleted($request)]);
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

        $data = $this->Reservation->read([
            'user_id' => $args['userID'],
            'deleted' => $this->deleted($request)
        ]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET building/{building_id}/reservations
    public function getReservationsInBuilding(Request $request, Response $response, $args): Response
    {
        $data = $this->Reservation->read([
            'building_id' => $args['building_id'],
            'deleted' => $this->deleted($request)
        ]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response;
    }

    // GET building/{building_id}/rooms/{room_id}/reservations
    public function getRoomReservations(Request $request, Response $response, $args): Response
    {
        $data = $this->Reservation->read([
            'building_id' => $args['building_id'],
            'room_id' => $args['room_id'],
            'deleted' => $this->deleted($request)
        ]);
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
        $currentUserMail = $request->getAttribute('email');

        $this->Reservation->update($reservationID, $data);
        
        $dataString = implode(',', array_keys($data));
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $reservationID,
            'message' => "User $currentUserMail updated reservation data: $dataString"
        ]);
        return $response->withStatus(200, "Succesfully updated");
    }

    // DELETE /reservations/{reservation_id}
    public function deleteReservationsByID(Request $request, Response $response, $args): Response
    {
        $currentUser = $request->getAttribute('user_id');
        $currentUserMail = $request->getAttribute('email');
        $reservationID = (int)$args['reservation_id'];

        $reservation = $this->Reservation->read(['id' => $reservationID])[0];

        if ($reservation['deleted'] === false) {
            $this->Reservation->update($reservationID, ['deleted' => true]);
            $this->Log->create([
                'user_id' => (int)$currentUser,
                'reservation_id' => $reservationID,
                'message' => "User $currentUserMail moved reservation to trash"
            ]);
        } else {
            $this->Log->create([
                'user_id' => (int)$currentUser,
                'reservation_id' => $reservationID,
                'message' => "User $currentUserMail hard deleted reservation"
            ]);

            $this->Reservation->delete((int) $args['reservation_id']);
        }

        return $response->withStatus(204, "Succesfully deleted");
    }
}
