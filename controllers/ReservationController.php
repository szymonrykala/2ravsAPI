<?php
namespace controllers;

use models\HttpConflictException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use models\Reservation;
use utils\Validator;

class ReservationController extends Controller
{
    /**
     * Implement endpoints related with reservations
     */
    private Reservation $Reservation;

    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Reservation = $this->DIcontainer->get(Reservation::class);
    }

    public function validateReservation(Request &$request, array &$data): void
    {
        /**
         * Validate Reservation
         * 
         * @param array $data
         * @throws HttpBadRequestException
         */
        $Validator = $this->DIcontainer->get(Validator::class);

        if (isset($data['subtitle'])) {
            if (!$Validator->validateString($data['subtitle'], 3)) {
                throw new HttpBadRequestException($request, 'Incorrect reservation ' . 'subtitle' . ' value (min 3 char. length).');
            } else {
                $data['subtitle'] = $Validator->sanitizeString($data['subtitle']);
            }
        }

        if (
            isset($data['title']) &&
            !$Validator->validateClearString($data['title'])
        ) throw new HttpBadRequestException($request, 'Incorrect reservation title format; pattern: ' . $Validator->clearString);

        foreach (['end_time', 'start_time'] as $item) {
            if (isset($data[$item])) {
                if (!$Validator->validateTime($data[$item])) {
                    throw new HttpBadRequestException($request, 'Incorrect ' . $item . ' format; pattern: hh:mm:ss.');
                }
            }
        }
        if (isset($data['date'])) {
            if (!$Validator->validateDate($data['date'])) {
                throw new HttpBadRequestException($request, 'Incorrect date format; pattern: yyyy-mm-dd.');
            }
        }
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
        ['params' => $params, 'mode' => $mode] = $this->getSearchParams($request);
        if (isset($params) && isset($mode))  $this->Reservation->setSearch($mode, $params);

        $this->Reservation->setQueryStringParams($this->parsedQueryString($request));

        $this->switchKey($args, 'reservation_id', 'id');
        $this->switchKey($args, 'userID', 'user_id');
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
        $reservation = $this->Reservation->read(['id' => $args['reservation_id']])[0];

        if ((bool)$reservation['confirmed'] === true) throw new HttpConflictException('Reservation is already confirmed');

        $this->Reservation->update($reservation['id'], ['confirmed' => true]);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $args['reservation_id'],
            'message' => 'User ' . $request->getAttribute('email') . ' confirmed reservation',
        ]);
        $response->getBody()->write("Reservation confirmed");
        return $response;
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

        $data = $this->getFrom($request, [
            "title" => 'string',
            "subtitle" => "string",
            "start_time" => "string",
            "end_time" => "string",
            "date" => "string"
        ], true);

        $this->validateReservation($request, $data);

        $reservationData = array_merge($data, [
            "room_id" => $roomID,
            "building_id" => $buildingID,
            "user_id" => $currentUser
        ]);

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

        $reservation = $this->Reservation->read(['id' => $args['reservation_id']])[0];
        if ($reservation['confirmed']) throw new HttpForbiddenException($request, 'Reservation You want to update is confirmed already. You can ot update confirmed Reservation');

        $data = $this->getFrom($request, [
            "title" => 'string',
            "subtitle" => "string",
            "start_time" => "string",
            "end_time" => "string",
            "date" => "string"
        ], false);

        $this->validateReservation($request, $data);

        $currentUserMail = $request->getAttribute('email');

        $this->Reservation->update($args['reservation_id'], $data);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $args['reservation_id'],
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
