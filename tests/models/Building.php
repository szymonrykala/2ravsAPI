<?php
require_once __DIR__ . '/../../config/config.php';

$db = new Database();
$building = new Building($db);

echo "building list: \n";
var_dump($building->read());

echo "creating building: \n";
/* {
    "address_id" => 2,
    "name" => "Budynek B",
    "rooms_count" => 30,
} */
$data = array(
    "address_id" => 2,
    "name" => "Budynek B",
    "rooms_count" => 35,
);
// $lastInsert = $building->create($data);
$building->update(3,$data);

echo "building list: \n";
var_dump($building->read());
