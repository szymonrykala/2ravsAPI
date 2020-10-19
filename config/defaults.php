<?php
// Error reporting
error_reporting(1);
ini_set('display_errors', '1');

date_default_timezone_set("Europe/Warsaw");

$settings = [];

$settings['root'] = dirname(__DIR__);

$settings['default_params'] = [
    'access' => 1
];

$settings['Database'] = [
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'name' => 'ravs_dev',
    'charset' => 'utf8mb4'
];

$settings['mail'] = [
    'send_from' => 'noreply@maciejkossowski.com'
];

//if `is_expire` is true, then tokens have expire time specified by `valid_time`
$settings['jwt'] = [
    'signature' => 'r@f@#dog#l435eks#kej4$*%$ci%w5fg5g4ghf^i^3456&o7zdgdfciesko',
    'is_expire' => false,
    'valid_time' => 3600 * 24 * 1,
    'ip_controll' => true
];

return $settings;
