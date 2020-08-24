<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Psr7\Response;


require_once __DIR__ . '/../config/config.php';

$DIcontainer = new Container(); //crceate DI container

AppFactory::setContainer($DIcontainer); //setting container

$rootApp = AppFactory::create(); // create the app


$rootApp->addRoutingMiddleware();
$rootApp->addBodyParsingMiddleware();
// $rootApp->addErrorMiddleware(true, true, true);
// $rootApp->add(new JSONMiddleware());

/*  CLIENT  -->  (JSONMiddleware)  -->  (AuthorizationMiddleware)  -->  (AccesMiddleware)  -->  API resources
    CLIENT  <--  (JSONMiddleware)  <--  (AuthorizationMiddleware)  <--  (AccesMiddleware)  <--  API resources  */
// $rootApp->add(new AccesMiddleware());

$rootApp->add(new JSONMiddleware());

//Models
//each Model have it's own connection to Database
$DIcontainer->set('Building', new Building(new Database()));
$DIcontainer->set('Acces', new Acces(new Database()));
$DIcontainer->set('Log', new Log(new Database()));
$DIcontainer->set('Reservation', new Reservation(new Database()));
$DIcontainer->set('Room', new Room(new Database()));
$DIcontainer->set('User', new User(new Database()));
$DIcontainer->set('Address', new Address(new Database()));
$DIcontainer->set('RoomType', new RoomType(new Database()));
$DIcontainer->set('View', new View());
// $DIcontainer->set('Mail', \Mail::class);

function myErrorHandler(Exception $e)
{
    $data = [
        'succes' => false,
        'status' => $e->httpCode,
        'error' => [
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ]
    ];
    http_response_code($e->httpCode);
    header('content-type:application/json');
    echo json_encode($data);
    return true;
}

set_exception_handler("myErrorHandler");


// $rootApp->any('/', function (Request $request, Response $response, $args) {
//     // $name = $args['name'];
//     $response->getBody()->write('Start');
//     return $response->withHeader('content-type', 'application/json');
// });


$rootApp->post('/auth', \UserController::class . ':verifyUser'); //open endpoint
$rootApp->post('/users', \UserController::class . ':registerNewUser'); // open endpoint
$rootApp->get('/users/activate', \UserController::class . ':activateUser'); // open endpoint

$rootApp->group('', function (\Slim\Routing\RouteCollectorProxy $app) {

    $app->group('/logs', function (\Slim\Routing\RouteCollectorProxy $logs) {
        $logs->get('', \LogController::class . ':getAllLogs');
        $logs->get('/search', \LogController::class . ':searchLogs');
        $logs->delete('/{log_id:.*[0-9]+}', \LogController::class . ':deleteLogByID');
    });

    $app->group('/addresses', function (\Slim\Routing\RouteCollectorProxy $addresses) {
        $addresses->get('', \AddressController::class . ':getAllAddresses');
        $addresses->post('', \AddressController::class . ':createAddress');

        $addresses->group('/{address_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $address) {
            $address->get('', \AddressController::class . ':getAddress');
            $address->patch('', \AddressController::class . ':updateAddress');
            $address->delete('', \AddressController::class . ':deleteAddress');
        });
    });

    $app->group('/acces', function (\Slim\Routing\RouteCollectorProxy $acceses) {
        $acceses->get('', \AccesController::class . ':getAllAccesTypes');
        $acceses->post('', \AccesController::class . ':createNewAccesType');
        
        $acceses->group('/{acces_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $acces) {
            $acces->get('', \AccesController::class . ':getAccesTypeByID');
            $acces->patch('', \AccesController::class . ':updateAccesType');
            $acces->delete('', \AccesController::class . ':deleteAccesType');
        });
    });

    $app->group('/reservations', function (\Slim\Routing\RouteCollectorProxy $reservations) {
        $reservations->get('', \ReservationController::class . ':getAllReservations');
        $reservations->post('', \ReservationController::class . ':createReservation');
        $reservations->delete('/{reservation_id:.*[0-9]+}', \ReservationController::class . ':deleteReservationsByID');

        $reservations->group('/{reservation_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $reservation) {
            $reservation->get('', \ReservationController::class . ':getReservationByID');
            $reservation->patch('', \ReservationController::class . ':updateReservationByID');
            $reservation->patch('/confirm', \ReservationController::class . ':confirmReservation');
        });
    });

    $app->group('/users', function (\Slim\Routing\RouteCollectorProxy $user) {

        $user->get('', \UserController::class . ':getAllUsers');

        $user->group('/{userID:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $specUser) {
            $specUser->get('', \UserController::class . ':getSpecificUser');
            $specUser->patch('', \UserController::class . ':updateUserInformations');
            $specUser->delete('', \UserController::class . ':deleteUser');
            $specUser->get('/reservations', \ReservationController::class . ':getUserReservations');
        });
    });

    $app->group('/buildings', function (\Slim\Routing\RouteCollectorProxy $buildings) {
        $buildings->get('', \BuildingController::class . ':getAllBuildings');
        $buildings->get('/search', \BuildingController::class . ':searchBuildings');
        $buildings->post('', \BuildingController::class . ':createNewBuilding');

        $buildings->group('/{buildingID:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $specBuilding) {
            $specBuilding->get('', \BuildingController::class . ':getBuildingByID');
            $specBuilding->patch('', \BuildingController::class . ':updateBuilding');
            $specBuilding->delete('', \BuildingController::class . ':deleteBuilding');
            $specBuilding->get('/reservations', \ReservationController::class . ':getReservationsInBuilding');

            $specBuilding->group('/rooms', function (\Slim\Routing\RouteCollectorProxy $rooms) {
                $rooms->get('', \RoomController::class . ':getAllRoomsInBuilding');
                $rooms->post('', \RoomController::class . ':createRoom');

                $rooms->group('/{room_id:[0-9]+}', function (\Slim\Routing\RouteCollectorProxy $room) {
                    $room->get('', \RoomController::class . ':getRoomByID');
                    $room->patch('', \RoomController::class . ':updateRoomByID');
                    $room->delete('', \RoomController::class . ':deleteRoomByID'); //serveral ID's
                    // $room->get('/reservations', \ReservationController::class . ':getRoomReservations');
                    // $room->post('/reservations', \ReservationController::class . ':createReservation');
                });
            });
        });
    });
})->add(new AuthorizationMiddleware($DIcontainer))
    ->add(new JWTMiddleware());


// 404 error heandler 
$rootApp->any('{route:.*}', function (Request $request, Response $response) {

    $response->getBody()->write(json_encode(array(
        "succes" => false,
        "errorMessage" => "Request URI does not exist",
        "errorCode" => 404
    )));
    return $response->withStatus(404)->withHeader("content-type", "application/json");
});

$rootApp->run();
