<?php

use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

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
    'Building' => function (ContainerInterface $container) {
        return new Building($container->get(Database::class));
    },
    'Access' => function (ContainerInterface $container) {
        return new Access($container->get(Database::class));
    },
    'Log' => function (ContainerInterface $container) {
        return new Log($container->get(Database::class));
    },
    'Reservation' => function (ContainerInterface $container) {
        return new Reservation($container->get(Database::class));
    },
    'Room' => function (ContainerInterface $container) {
        return new Room($container->get(Database::class));
    },
    'User' => function (ContainerInterface $container) {
        return new User($container->get(Database::class));
    },
    'Address' => function (ContainerInterface $container) {
        return new Address($container->get(Database::class));
    },
    'RoomType' => function (ContainerInterface $container) {
        return new RoomType($container->get(Database::class));
    },
    'Validator' => new Validator(),
    'MailSender' => new MailSender(),
    'settings' => (require __DIR__ . '/defaults.php')
];
