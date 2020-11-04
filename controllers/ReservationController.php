<?php

namespace controllers;

use models\HttpConflictException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use models\Reservation;

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

        if ($reservation['confirmed']) throw new HttpConflictException('Reservation is already confirmed');

        $this->Reservation->update(['confirmed' => true], $reservation['id'],);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $args['reservation_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE reservation DATA ' . json_encode(['confirmed' => true])
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
        $data = $this->getParsedData($request);

        $reservationData = array_merge($data, [
            'room_id' => (int)$args['room_id'],
            'building_id' => (int)$args['building_id'],
            'user_id' => $request->getAttribute('user_id')
        ]);

        $reservationData['id'] = $this->Reservation->create($reservationData);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $reservationData['id'],
            'room_id' => $args['room_id'],
            'building_id' => $args['building_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' CREATE reservation DATA ' . json_encode($reservationData)
        ]);

        return $response->withStatus(201, 'Created');
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
        if ($reservation['confirmed']) throw new HttpForbiddenException($request, 'Reservation You want to update is confirmed already. You can not update confirmed Reservation');

        $data = $this->getParsedData($request);

        $this->Reservation->update($data, $args['reservation_id']);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $args['reservation_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE reservation DATA ' . json_encode($data)
        ]);
        return $response->withStatus(204, 'Updated');
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

        $reservation = $this->Reservation->read(['id' => $args['reservation_id']])[0];

        $this->Reservation->delete((int) $args['reservation_id']);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $args['reservation_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE reservation DATA ' . json_encode($reservation)
        ]);

        return $response->withStatus(204, 'Deleted');
    }
}
