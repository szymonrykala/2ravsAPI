<?php
require_once __DIR__ . '/../../config/config.php';

$db = new Database();
$log = new Log($db);

// echo "list of logs: \n";
// var_dump($log->read());

echo "creating \n";

/* {
    "message" => "testowy log tworzenie pokoju",
    "user_id" => 1,
    "room_id" => 2,
    "building_id" => null,
    "reservation_id" => null
} */
$insertLog = $log->create(array(
    'message' => 'testowy log tworzenie pokoju',
    'user_id' => 4,
    'room_id' => 2
));

echo "Logs after add test log";
var_dump($log->read());
