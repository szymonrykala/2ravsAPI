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
        $JWTsignature = $container->get('settings')['jwt']['signature'];
        return new JWTMiddleware($JWTsignature);
    },
    JSONMiddleware::class => function (ContainerInterface $container) {
        return new JSONMiddleware();
    },
    AuthorizationMiddleware::class => function (ContainerInterface $container) {
        return new AuthorizationMiddleware($container);
    },
    Building::class => function (ContainerInterface $container) {
        return new Building($container->get(Database::class));
    },
    Access::class => function (ContainerInterface $container) {
        return new Access($container->get(Database::class));
    },
    Log::class => function (ContainerInterface $container) {
        return new Log($container->get(Database::class));
    },
    Reservation::class => function (ContainerInterface $container) {
        return new Reservation($container->get(Database::class));
    },
    Room::class => function (ContainerInterface $container) {
        return new Room($container->get(Database::class));
    },
    User::class => function (ContainerInterface $container) {
        return new User($container->get(Database::class));
    },
    Address::class => function (ContainerInterface $container) {
        return new Address($container->get(Database::class));
    },
    RoomType::class => function (ContainerInterface $container) {
        return new RoomType($container->get(Database::class));
    },
    Validator::class => function (ContainerInterface $container) {
        return new Validator();
    },
    MailSender::class => function (ContainerInterface $container) {
        return new MailSender();
    },
    'settings' => (require_once __DIR__ . '/defaults.php')
];
