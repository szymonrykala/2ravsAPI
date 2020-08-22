<?php
require_once __DIR__ . "/../../config/config.php";

$db = new Database();
$room = new Room($db);

/* {
                "name" : 'b202',
                "floor" : 2,
                "room_type_id" : 1,
                "seats_count" : 60,
                "equipment" : "rzutnik,kreda,tablica",
                "building_id" : 3
} */

echo "All rooms list \n";
var_dump($room->read());

$data = array(
    "name" => 'A202',
    "floor" => 2,
    "room_type_id" => 1,
    "seats_count" => 60,
    "equipment" => "rzutnik,kreda,tablica",
    "building_id" => 2
);
echo "Creating room \n";
$lastIndex = $room->create($data);
echo "Index of inserted item is '$lastIndex'";


echo "All rooms list \n";
var_dump($room->read());
