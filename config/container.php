<?php

use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use middleware\AuthorizationMiddleware;
use middleware\JSONMiddleware;
use middleware\JWTMiddleware;
use models\Access;
use models\Address;
use models\Building;
use models\Log;
use models\Reservation;
use models\Room;
use models\RoomType;
use models\User;
use utils\Validator;
use utils\Database;
use utils\MailSender;


return [
    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },
    Database::class => function (ContainerInterface $container) {
        $db = $container->get('settings')['Database'];
        return new Database(
            $db['user'],
            $db['password'],
            $db['host'],
            $db['name'],
            $db['charset'],
        );
    },
    JWTMiddleware::class => function (ContainerInterface $container) {
        $JWTsettings = $container->get('settings')['jwt'];
        return new JWTMiddleware($JWTsettings);
    },
    JSONMiddleware::class => function (ContainerInterface $container) {
        return new JSONMiddleware();
    },
    AuthorizationMiddleware::class => function (ContainerInterface $container) {
        return new AuthorizationMiddleware($container);
    },
    Building::class => function (ContainerInterface $container) {
        $schema = require __DIR__ .'/../models/schemas/Building.php';
        return new Building($container->get(Database::class),$schema);
    },
    Access::class => function (ContainerInterface $container) {
        $schema = require __DIR__ .'/../models/schemas/Access.php';
        return new Access($container->get(Database::class),$schema);
    },
    Log::class => function (ContainerInterface $container) {
        $schema = require __DIR__ .'/../models/schemas/Log.php';
        return new Log($container->get(Database::class),$schema);
    },
    Reservation::class => function (ContainerInterface $container) {
        $schema = require __DIR__ .'/../models/schemas/Reservation.php';
        return new Reservation($container->get(Database::class),$schema);
    },
    Room::class => function (ContainerInterface $container) {
        $schema = require __DIR__ .'/../models/schemas/Room.php';
        return new Room($container->get(Database::class),$schema);
    },
    User::class => function (ContainerInterface $container) {
        $schema = require __DIR__ .'/../models/schemas/User.php';
        return new User($container->get(Database::class),$schema);
    },
    Address::class => function (ContainerInterface $container) {
        $schema = require __DIR__ .'/../models/schemas/Address.php';
        return new Address($container->get(Database::class),$schema);
    },
    RoomType::class => function (ContainerInterface $container) {
        $schema = require __DIR__ .'/../models/schemas/RoomType.php';
        return new RoomType($container->get(Database::class),$schema);
    },
    Validator::class => function (ContainerInterface $container) {
        return new Validator();
    },
    MailSender::class => function (ContainerInterface $container) {
        return new MailSender($container->get('settings')['mail']);
    },
    'settings' => (require_once __DIR__ . '/defaults.php')
];
