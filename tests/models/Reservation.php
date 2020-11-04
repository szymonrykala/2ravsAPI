<?php
require_once __DIR__ . '/../../config/config.php';

$db = new Database();
$reservation = new Reservation($db);

// echo "List of reservations: \n";
// var_dump($reservation->read());

$data = array(
    'user_id' => 6,
    'building_id' => 3,
    'room_id' => 3,
    'title' => "rezerwacja próbna",
    'subtitle' => "test - próbna rezerwacja",
    'start_time' => "12:00",
    'end_time' => "13:30",
    'date' => "2020-08-15"
);
echo "creating new reservation: \n";
$insertIndex = $reservation->create($data);

echo "List of reservations: \n";
var_dump($reservation->read());

echo "updating last reservation: \n";
$reservation->update($insertIndex, array('subtitle'=>"update - próbna rezerwacja"));

echo "List of reservations: \n";
var_dump($reservation->read());
