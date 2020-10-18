<?php
use DI\ContainerBuilder;

//composer loading
require __DIR__ . '/../vendor/autoload.php';

// namespaces loading
spl_autoload_register(require __DIR__ . '/../config/autoloader.php');

// setting error handler
set_exception_handler(require __DIR__.'/../utils/error_handler.php');

//crceate DI container
$containerBuilder = new ContainerBuilder(); 
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');

$container = $containerBuilder->build();

$app = $container->get(\App::class);

//registering routes and middlewares
(require __DIR__ . '/../config/router.php')($app, $container);

return $app;