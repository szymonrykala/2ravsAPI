<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;


require_once __DIR__ . '/../config/config.php';

$DIcontainer = new Container(); //crceate DI container

AppFactory::setContainer($DIcontainer); //setting container

$rootApp = AppFactory::create(); // create the app


$rootApp->addRoutingMiddleware();
$rootApp->addBodyParsingMiddleware();
// $rootApp->addErrorMiddleware(true, true, true);
// $rootApp->add(new JSONMiddleware());

/*  CLIENT  -->  (JSONMiddleware)  -->  (AuthorizationMiddleware)  -->  (AccessMiddleware)  -->  API resources
    CLIENT  <--  (JSONMiddleware)  <--  (AuthorizationMiddleware)  <--  (AccessMiddleware)  <--  API resources  */
// $rootApp->add(new AccessMiddleware());



//Models
//each Model have it's own connection to Database
$DIcontainer->set('Building', new Building(new Database()));
$DIcontainer->set('Access', new Access(new Database()));
$DIcontainer->set('Log', new Log(new Database()));
$DIcontainer->set('Reservation', new Reservation(new Database()));
$DIcontainer->set('Room', new Room(new Database()));
$DIcontainer->set('User', new User(new Database()));
$DIcontainer->set('Address', new Address(new Database()));
$DIcontainer->set('RoomType', new RoomType(new Database()));
$DIcontainer->set('View', new View());
// $DIcontainer->set('Mail', \Mail::class);

function myErrorHandler(Throwable $e)
{
    $data = [
        'error' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            // 'trace' => $e->getTrace(),
            'code' => $e->getCode()
        ]
    ];
    http_response_code($e->getCode());
    header('content-type:application/json');
    echo json_encode($data);
    return true;
}
set_exception_handler("myErrorHandler");



$rootApp->post('/auth', \UserController::class . ':verifyUser'); //open endpoint
$rootApp->post('/users', \UserController::class . ':registerNewUser'); // open endpoint
$rootApp->get('/users/activate', \UserController::class . ':activateUser'); // open endpoint

$rootApp->group('', function (\Slim\Routing\RouteCollectorProxy $app) {

    $app->group('/logs', function (\Slim\Routing\RouteCollectorProxy $logs) {
        $logs->get('', \LogController::class . ':getLogs');
        $logs->get('/{id:[0-9]+}', \LogController::class . ':getLogs');
        $logs->get('/search', \LogController::class . ':searchLogs');
        $logs->delete('/{log_id:.*[0-9]+}', \LogController::class . ':deleteLogByID');
    });

    $app->group('/addresses', function (\Slim\Routing\RouteCollectorProxy $addresses) {
        $addresses->get('', \AddressController::class . ':getAddresses');
        $addresses->post('', \AddressController::class . ':createAddress');

        $addresses->group('/{address_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $address) {
            $address->get('', \AddressController::class . ':getAddresses');
            $address->patch('', \AddressController::class . ':updateAddress');
            $address->delete('', \AddressController::class . ':deleteAddress');
        });
    });

    $app->group('/access', function (\Slim\Routing\RouteCollectorProxy $accesses) {
        $accesses->get('', \AccessController::class . ':getAccessTypes');
        $accesses->post('', \AccessController::class . ':createNewAccessType');

        $accesses->group('/{access_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $access) {
            $access->get('', \AccessController::class . ':getAccessTypes');
            $access->patch('', \AccessController::class . ':updateAccessType');
            $access->delete('', \AccessController::class . ':deleteAccessType');
        });
    });

    $app->group('/reservations', function (\Slim\Routing\RouteCollectorProxy $reservations) {
        $reservations->get('', \ReservationController::class . ':getReservations');
        $reservations->get('/search', \ReservationController::class . ':searchReservations');

        // $reservations->post('', \ReservationController::class . ':createReservation');
        $reservations->delete('/{reservation_id:.*[0-9]+}', \ReservationController::class . ':deleteReservationsByID');

        $reservations->group('/{reservation_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $reservation) {
            $reservation->get('', \ReservationController::class . ':getReservations');
            $reservation->patch('', \ReservationController::class . ':updateReservationByID');
            $reservation->patch('/confirm', \ReservationController::class . ':confirmReservation');
        });
    });

    $app->group('/users', function (\Slim\Routing\RouteCollectorProxy $user) {

        $user->get('', \UserController::class . ':getUsers');
        $user->get('/search', \UserController::class . ':searchUsers');

        $user->group('/{userID:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $specUser) {
            $specUser->get('', \UserController::class . ':getUsers');
            $specUser->patch('', \UserController::class . ':updateUserInformations');
            $specUser->delete('', \UserController::class . ':deleteUser');
            $specUser->get('/reservations', \ReservationController::class . ':getReservations');
        });
    });

    $app->group('/buildings', function (\Slim\Routing\RouteCollectorProxy $buildings) {
        $buildings->get('', \BuildingController::class . ':getBuildings');
        $buildings->get('/search', \BuildingController::class . ':searchBuildings');
        $buildings->post('', \BuildingController::class . ':createBuilding');

        $buildings->group('/rooms', function (\Slim\Routing\RouteCollectorProxy $rooms) {
            $rooms->get('/types', \RoomTypeController::class . ':getAllTypes');
            $rooms->post('/types', \RoomTypeController::class . ':createType');
            $rooms->patch('/types/{room_type_id:[0-9]+}', \RoomTypeController::class . ':updateType');
            $rooms->delete('/types/{room_type_id:[0-9]+}', \RoomTypeController::class . ':deleteType');

            $rooms->get('', \RoomController::class . ':getRooms');
            $rooms->get('/{room_id}', \RoomController::class . ':getRoom');
        });

        $buildings->group('/{building_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $specBuilding) {
            $specBuilding->get('', \BuildingController::class . ':getBuildings');
            $specBuilding->patch('', \BuildingController::class . ':updateBuilding');
            $specBuilding->delete('', \BuildingController::class . ':deleteBuilding');
            $specBuilding->get('/reservations', \ReservationController::class . ':getReservations');
            $specBuilding->get('/reservations/search', \ReservationController::class . ':searchReservations');

            $specBuilding->group('/rooms', function (\Slim\Routing\RouteCollectorProxy $rooms) {
                $rooms->get('', \RoomController::class . ':getRooms');
                $rooms->post('', \RoomController::class . ':createRoom');

                $rooms->group('/{room_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $room) {
                    $room->get('', \RoomController::class . ':getRooms');
                    $room->patch('', \RoomController::class . ':updateRoomByID');
                    $room->delete('', \RoomController::class . ':deleteRoomByID');
                    $room->get('/reservations', \ReservationController::class . ':getReservations');
                    $room->post('/reservations', \ReservationController::class . ':createReservation');
                });
            });
        });
    });
})->add(new AuthorizationMiddleware($DIcontainer))
    ->add(new JWTMiddleware());

// 404 error heandler 
$rootApp->any('{route:.*}', function (Request $request, Response $response) {

    throw new HttpNotFoundException($request, "Requested URL does not exist");
});

$rootApp->add(new JSONMiddleware());

$rootApp->run();
