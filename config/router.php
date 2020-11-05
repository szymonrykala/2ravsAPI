<?php

use DI\Container;
use Slim\App;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use middleware\AuthorizationMiddleware;
use middleware\JSONMiddleware;
use middleware\JWTMiddleware;
use Slim\Exception\HttpNotFoundException;

use controllers\UserController;
use controllers\BuildingController;
use controllers\AccessController;
use controllers\AddressController;
use controllers\RoomController;
use controllers\RoomTypeController;
use controllers\ReservationController;
use controllers\LogController;

return function (App $app, Container $DIcontainer) {

    $app->get('/v1/info', function (Request $request, Response $response): Response {
        $response->getBody()->write(file_get_contents(__DIR__.'/info.json'));
        return $response->withHeader('content-type', 'application/json');
    });

    $app->post('/v1/auth', UserController::class . ':verifyUser'); //open endpoint
    $app->post('/v1/users', UserController::class . ':registerNewUser'); // open endpoint
    $app->patch('/v1/users/action', UserController::class . ':userAction'); // open endpoint

    $app->group('/v1', function (\Slim\Routing\RouteCollectorProxy $v1) {
        $v1->patch('/rfid', RoomController::class . ':rfidAction');

        $v1->group('/logs', function (\Slim\Routing\RouteCollectorProxy $logs) {
            $logs->get('', LogController::class . ':getLogs');
            $logs->get('/{log_id:[0-9]+}', LogController::class . ':getLogs');
            $logs->delete('/{log_id:.*[0-9]+}', LogController::class . ':deleteLogByID');
        });

        $v1->group('/addresses', function (\Slim\Routing\RouteCollectorProxy $addresses) {
            $addresses->get('', AddressController::class . ':getAddresses');
            $addresses->post('', AddressController::class . ':createAddress');

            $addresses->group('/{address_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $address) {
                $address->get('', AddressController::class . ':getAddresses');
                $address->patch('', AddressController::class . ':updateAddress');
                $address->delete('', AddressController::class . ':deleteAddress');
            });
        });

        $v1->group('/access', function (\Slim\Routing\RouteCollectorProxy $accesses) {
            $accesses->get('', AccessController::class . ':getAccessTypes');
            $accesses->post('', AccessController::class . ':createNewAccessType');

            $accesses->group('/{access_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $access) {
                $access->get('', AccessController::class . ':getAccessTypes');
                $access->patch('', AccessController::class . ':updateAccessType');
                $access->delete('', AccessController::class . ':deleteAccessType');
            });
        });

        $v1->group('/reservations', function (\Slim\Routing\RouteCollectorProxy $reservations) {
            $reservations->get('', ReservationController::class . ':getReservations');

            $reservations->delete('/{reservation_id:.*[0-9]+}', ReservationController::class . ':deleteReservationsByID');

            $reservations->group('/{reservation_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $reservation) {
                $reservation->get('', ReservationController::class . ':getReservations');
                $reservation->patch('', ReservationController::class . ':updateReservationByID');
                $reservation->patch('/confirm', ReservationController::class . ':confirmReservation');
            });
        });

        $v1->group('/users', function (\Slim\Routing\RouteCollectorProxy $user) {

            $user->get('', UserController::class . ':getUsers');

            $user->group('/{userID:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $specUser) {
                $specUser->get('', UserController::class . ':getUsers');
                $specUser->patch('', UserController::class . ':updateUserInformations');
                $specUser->delete('', UserController::class . ':deleteUser');
                $specUser->get('/reservations', ReservationController::class . ':getReservations');
            });
        });

        $v1->group('/buildings', function (\Slim\Routing\RouteCollectorProxy $buildings) {
            $buildings->get('', BuildingController::class . ':getBuildings');
            $buildings->post('', BuildingController::class . ':createBuilding');

            $buildings->group('/rooms', function (\Slim\Routing\RouteCollectorProxy $rooms) {
                $rooms->get('', RoomController::class . ':getRooms');
                $rooms->group('/{room_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $room) {
                    $room->get('', RoomController::class . ':getRooms');
                    $room->patch('', RoomController::class . ':updateRoom');
                    $room->delete('', RoomController::class . ':deleteRoom');
                });

                $rooms->get('/types', RoomTypeController::class . ':getTypes');
                $rooms->post('/types', RoomTypeController::class . ':createType');
                $rooms->patch('/types/{room_type_id:[0-9]+}', RoomTypeController::class . ':updateType');
                $rooms->delete('/types/{room_type_id:[0-9]+}', RoomTypeController::class . ':deleteType');
            });
            
            $buildings->group('/{building_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $specBuilding) {
                $specBuilding->get('', BuildingController::class . ':getBuildings');
                $specBuilding->patch('', BuildingController::class . ':updateBuilding');
                $specBuilding->delete('', BuildingController::class . ':deleteBuilding');
                $specBuilding->get('/reservations', ReservationController::class . ':getReservations');

                $specBuilding->group('/rooms', function (\Slim\Routing\RouteCollectorProxy $rooms) {
                    $rooms->get('', RoomController::class . ':getRooms');
                    $rooms->post('', RoomController::class . ':createRoom');

                    $rooms->group('/{room_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $room) {
                        $room->get('', RoomController::class . ':getRooms');

                        $room->get('/reservations', ReservationController::class . ':getReservations');
                        $room->post('/reservations', ReservationController::class . ':createReservation');
                    });
                });
            });
        });
    })->add($DIcontainer->get(AuthorizationMiddleware::class))
        ->add($DIcontainer->get(JWTMiddleware::class));

    $app->any('{route:.*}', function (Request $request, Response $response) {
        throw new HttpNotFoundException($request, "Requested URL does not exist");
    });
    $app->add($DIcontainer->get(JSONMiddleware::class));
    $app->addRoutingMiddleware();
    $app->addBodyParsingMiddleware();
};
