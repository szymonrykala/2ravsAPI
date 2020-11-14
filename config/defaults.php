<?php
// Error reporting
error_reporting(1);
ini_set('display_errors', '1');

date_default_timezone_set("Europe/Warsaw");

$settings = [];

$settings['root'] = dirname(__DIR__);

$settings['default_params'] = [
    'access' => 1,
    'room_blockade' => true //default state of room after create
];

$settings['Database'] = [
    'user' => 'root',
    'password' => '',
    'host' => '127.0.0.1',
    'name' => 'ravs_test',
    'charset' => 'utf8mb4'
];

$settings['mail'] = [
    'send_from' => 'noreply@maciejkossowski.com'
];

$settings['UserController'] = [
    //if `is_expire` is true, then tokens have expire time specified by `valid_time`
    'jwt' => [
        'signature' => 'r@f@#dog#l435eks#kej4$*%$ci%w5fg5g4ghf^i^3456&o7zdgdfciesko',
        'is_expire' => false,
        'valid_time' => 3600 * 24 * 1,
        'ip_controll' => true
    ],
    'activation_key_len' => 6
];

return $settings;
