<?php
// Configure defaults for the whole application.

// Error reporting
error_reporting(1);
ini_set('display_errors', '1');

date_default_timezone_set("Europe/Warsaw");

$settings = [];

$settings['root'] = dirname(__DIR__);

$settings['Database'] = [
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'name' => 'ravs_dev',
    'charset' => 'utf8mb4'
];

$settings['mail']=[

];

$settings['jwt'] = [
    'secret' => 'r@f@#dog#l435eks#kej4$*%$ci%w5fg5g4ghf^i^3456&o7zdgdfciesko'
];

return $settings;
