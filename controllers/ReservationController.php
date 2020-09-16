<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once __DIR__ . "/Controller.php";

class ReservationController extends Controller
{
    /**
     * Implement endpoints related with reservations
     */
    private $Reservation;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Reservation = $this->DIcontainer->get('Reservation');
    }

    // GET /reservations
    public function getAllReservations(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all reservations in database,
         * returning array of items
         * GET /reservations
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $data = $this->Reservation->read(['deleted' => $this->deleted($request)]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // PATCH reservations/{reservation_id} 
    public function confirmReservation(Request $request, Response $response, $args): Response
    {
        /**
         * Confirm reservation,
         * PATCH reservations/{reservation_id}
         * { "confirmed":true }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        throw new APIException("Confirming Not implemented!", 501);
        $response->getBody()->write("Middleware");
        return $response;
    }

    // GET /reservations/{reservation_id}
    public function getReservationByID(Request $request, Response $response, $args): Response
    {
        /**
         * Getting one reservations by reservation_id,
         * returning one items
         * GET /reservations/{reservation_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $data = $this->Reservation->read(['id' => $args['reservation_id']]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // GET /users/{userID}/reservations
    public function getUserReservations(Request $request, Response $response, $args): Response
    {
        /**
         * Get all reservations of sepcific user,
         * returning array of items
         * GET /users/{userID}/reservations
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $data = $this->Reservation->read([
            'user_id' => $args['userID'],
            'deleted' => $this->deleted($request)
        ]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // GET building/{building_id}/reservations
    public function getReservationsInBuilding(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all reservations in building by building_id,
         * returning array of items
         * GET building/{building_id}/reservations
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $data = $this->Reservation->read([
            'building_id' => $args['building_id'],
            'deleted' => $this->deleted($request)
        ]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // GET building/{building_id}/rooms/{room_id}/reservations
    public function getRoomReservations(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all reservations in room which is in given building ,
         * returning array of items
         * GET building/{building_id}/rooms/{room_id}/reservations
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $data = $this->Reservation->read([
            'building_id' => $args['building_id'],
            'room_id' => $args['room_id'],
            'deleted' => $this->deleted($request)
        ]);
        $data = $this->handleExtensions($data, $request);

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }

    // POST /buildings/{building_id}/rooms/{room_id}/reservations
    public function createReservation(Request $request, Response $response, $args): Response
    {
        /**
         * Creating new reservation
         * returning 201
         * POST /buildings/{building_id}/rooms/{room_id}/reservations
         * {
         *     "title":"rezerwacja v1",
         *     "subtitle":"podtytuł rezerwacji, opis",
         *     "start_time":"10:00",
         *     "end_time":"11:15",
         *     "date":"2020-08-28"
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
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

        return $response->withStatus(201);
    }

    // PATCH /reservations/{reservation_id}
    public function updateReservationByID(Request $request, Response $response, $args): Response
    {
        /**
         * Updating reservation by reservation_id
         * returning 204
         * PATCH /reservations/{reservation_id}
         * {
         *     "title":"rezerwacja v1 update",
         *     "subtitle":"podtytuł rezerwacji, opis",
         *     "start_time":"10:00",
         *     "end_time":"11:15",
         *     "date":"2020-08-28"
         * }
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
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
        return $response->withStatus(204, "Updated");
    }

    // DELETE /reservations/{reservation_id}
    public function deleteReservationsByID(Request $request, Response $response, $args): Response
    {
        /**
         * deleting reservation by reservation_id
         * DELETE /reservations/{reservation_id}
         * returning 204
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
        */
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

        return $response->withStatus(204, "Deleted");
    }
}
