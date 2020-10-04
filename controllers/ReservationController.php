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
    // GET /reservations/{reservation_id}
    // GET /users/{userID}/reservations
    // GET building/{building_id}/reservations
    // GET building/{building_id}/rooms/{room_id}/reservations
    public function getReservations(Request $request, Response $response, $args): Response
    {
        /**
         * Getting all reservations in database,
         * returning array of items
         * GET /reservations
         * GET /reservations/{reservation_id}
         * GET /users/{userID}/reservations
         * GET building/{building_id}/reservations
         * GET building/{building_id}/rooms/{room_id}/reservations
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $this->Reservation->setQueryStringParams($this->parsedQueryString($request));

        $args['deleted'] = (bool)$this->parsedQueryString($request, 'deleted');
        if (isset($args['reservation_id'])) {
            $args['id'] = $args['reservation_id'];
            unset($args['reservation_id']);
        }
        $data = $this->handleExtensions($this->Reservation->read($args), $request);

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
        throw new Exception("Confirming Not implemented!", 501);
        $response->getBody()->write("Middleware");
        return $response;
    }

    // GET buildings/{building_id}/rooms/{room_id}/reservations/search
    // GET buildings/{building_id}/reservations/search
    // GET reservations/search
    public function searchReservations(Request $request, Response $response, $args): Response
    {
        /**
         * Searching for reservtions with parameters given in Request(query string or body['search'])
         * Founded results are written into the response body
         * GET /logs/search?<queryString>
         * { "search":{"key":"value","key2":"value2"}}
         * 
         * @param Request $request 
         * @param Response $response 
         * @param $args
         * 
         * @return Response 
         */
        $params = $this->getSearchParams($request);
        if (isset($args['building_id'])) {
            $params['building_id'] = $args['building_id'];
        }
        if (isset($args['room_id'])) {
            $params['room_id'] = $args['room_id'];
        }
        $data = $this->Reservation->search($params);

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

        $reservationData = [
            "title" => $title,
            "subtitle" => $subtitle,
            "start_time" => $startTime,
            "end_time" => $endTime,
            "date" => $date,
            "room_id" => $roomID,
            "building_id" => $buildingID,
            "user_id" => $currentUser
        ];
        $reservationID = $this->Reservation->create($reservationData);
        $this->Log->create([
            'user_id' => $currentUser,
            'reservation_id' => $reservationID,
            'room_id' => $roomID,
            'building_id' => $buildingID,
            'message' => "User $currentUserMail created reservation data:" . json_encode($reservationData)
        ]);

        return $response->withStatus(201, "Created");
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

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $reservationID,
            'message' => "User $currentUserMail updated reservation data:" . json_encode($data)
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
            $this->Reservation->delete((int) $args['reservation_id']);
            $this->Log->create([
                'user_id' => (int)$currentUser,
                'reservation_id' => $reservationID,
                'message' => "User $currentUserMail hard deleted reservation"
            ]);
        }

        return $response->withStatus(204, "Deleted");
    }
}
