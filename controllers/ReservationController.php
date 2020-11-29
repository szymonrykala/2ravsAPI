<?php

namespace controllers;

use models\Building;
use models\Room;
use models\HttpConflictException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Exception\HttpForbiddenException;
use models\Reservation;
use Slim\Exception\HttpNotFoundException;

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
        return parent::get($request, $response, $args);
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
        return $response->withStatus(204, 'Updated');
    }

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
    public function createReservation(Request $request, Response $response, $args): Response
    {
        $data = array_merge($this->getParsedData($request), [
            'room_id' => (int)$args['room_id'],
            'building_id' => (int)$args['building_id'],
            'user_id' => $request->getAttribute('user_id')
        ]);

        //building exist?
        $Building = $this->DIcontainer->get(Building::class);
        $Room = $this->DIcontainer->get(Room::class);

        if (!$Building->exist(['id' => $data['building_id']])) { //if not exist
            throw new HttpNotFoundException($request, "Specified building is not exist.");
        }

        //room exist in this building?
        $room = $Room->read(['room_id' => $data['room_id'], 'building_id' => $data['building_id']])[0];
        if (!$room) { //if not exist
            throw new HttpNotFoundException($request, 'Specified room is not exist in given building.');
        }

        //room is bookable?
        if ((bool)$room['blockade']) {
            throw new HttpConflictException('Specified room is not bookable. Room You want to reserve has blocked status.'); //conflict
        }

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
    public function updateReservationByID(Request $request, Response $response, $args): Response
    {
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
    public function deleteReservationsByID(Request $request, Response $response, $args): Response
    {
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
