<?php

namespace controllers;

use models\GenericModel;
use models\HttpConflictException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpForbiddenException;
use models\Reservation;

class ReservationController extends Controller
{
    /**
     * Implement endpoints related with reservations
     */
    public function __construct(ContainerInterface $DIcontainer)
    {
        parent::__construct($DIcontainer);
        $this->Model = $this->DIcontainer->get(Reservation::class);
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
        $this->switchKey($args, 'reservation_id', 'id');
        $this->switchKey($args, 'userID', 'user_id');
        return parent::get($request,$response,$args);
    }

    // PATCH reservations/{reservation_id}/confirm
    public function confirmReservation(Request $request, Response $response, $args): Response
    {
        /**
         * Confirm reservation,
         * PATCH reservations/{reservation_id}
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */
        $data = $this->getParsedData($request);

        $this->Model->data = $this->Model->read(['id' => $args['reservation_id']])[0];

        if ($this->Model->data['confirmed']) throw new HttpConflictException('Reservation is already confirmed');

        $this->Model->update(['confirmed' => $data['confirmed'], 'confirming_user_id' => $request->getAttribute('user_id')]);

        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $args['reservation_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' UPDATE reservation DATA ' . json_encode(['confirmed' => true])
        ]);
        return $response->withStatus(204,'Updated');
    }

    // POST /buildings/{building_id}/rooms/{room_id}/reservations
    public function createReservation(Request $request, Response $response, $args): Response
    {
        /**
         * Creating new reservation
         * returning 201
         * POST /buildings/{building_id}/rooms/{room_id}/reservations
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */

        $data = array_merge($this->getParsedData($request), [
            'room_id' => (int)$args['room_id'],
            'building_id' => (int)$args['building_id'],
            'user_id' => $request->getAttribute('user_id')
        ]);

        $data['id'] = $this->Model->create($data);
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $data['id'],
            'room_id' => (int)$args['room_id'],
            'building_id' => (int)$args['building_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' CREATE reservation DATA ' . json_encode($data)
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
         * 
         * @param Request $request
         * @param Response $response
         * @param array $array
         * 
         * @return Response $response
         */

        $this->Model->data = $this->Model->read(['id' => $args['reservation_id']])[0];

        if ($this->Model->data['confirmed']) throw new HttpForbiddenException($request, 'Reservation You want to update is confirmed already. You can not update confirmed Reservation');

        $data = $this->getParsedData($request);

        $this->Model->update($data);

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

        $this->Model->data = $this->Model->read(['id' => $args['reservation_id']])[0];

        $this->Model->delete();
        $this->Log->create([
            'user_id' => $request->getAttribute('user_id'),
            'reservation_id' => $args['reservation_id'],
            'message' => 'USER ' . $request->getAttribute('email') . ' DELETE reservation DATA ' . json_encode($this->Model->data)
        ]);

        return $response->withStatus(204, 'Deleted');
    }
}
