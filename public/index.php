<?php

use DI\ContainerBuilder;

// spl_autoload_register(require_once __DIR__.'/../config/autoloader.php');

// spl_autoload_extensions(".class.php"); // comma-separated list
// spl_autoload_register();

require __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(require __DIR__ . '/../config/autoloader.php');


require_once __DIR__ . '/../config/config.php';

$containerBuilder = new ContainerBuilder(); //crceate DI container
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');

$container = $containerBuilder->build();

$app = $container->get(\App::class);

(require __DIR__ . '/../config/router.php')($app, $container);

set_exception_handler(require __DIR__.'/../utils/error_handler.php');

$app->run();
