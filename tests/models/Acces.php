<?php
require_once __DIR__ . '/../../config/config.php';

$acces = new Acces($db);

/* {
    "name" => "demo",
    "acces_edit" => false,
    "logs_edit" => false,
    "logs_view" => false,
    "reservations_confirm" => false,
    "reservations_acces" => false,
    "reservations_edit" => false,
    "rooms_edit" => false,
    "rooms_view" => true,
    "buildings_edit" => false,
    "buildings_view" => true,
    "statistics_view" => false,
    "users_edit" => false
} */
$newAccesData =  array(
    'name' => 'demo',
    'acces_edit' => 0,
    'logs_edit' => 0,
    'logs_view' => 0,
    'reservations_confirm' => 0,
    'reservations_acces' => 0,
    'reservations_edit' => 0,
    'rooms_edit' => 0,
    'rooms_view' => 1,
    'buildings_edit' => 0,
    'buildings_view' => 1,
    'statistics_view' => 0,
    'users_edit' => 0
);
$lastAccesID = $acces->create($newAccesData);
echo "Last insert id: " . var_dump($lastAccesID);

$foundItem = $acces->search(array('name' => 'min'));
echo "List of acceses: " . var_dump($foundItem);

$accesList = $acces->read();
echo "List of acceses: " . var_dump($accesList);

$acces->delete($lastAccesID);
