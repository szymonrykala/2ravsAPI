<?php
require_once __DIR__ . '/../../config/config.php';

$db = new Database();

$user = new User($db);

/* {
    "name": "Szymon",
    "surname": "Rykała",
    "password": "demo",
    "email": "szymonrykalaDemo@gmail.com",
    "action_key": "xxxyyy222333",
    "acces_id": 1
} */

$userData =  array(
    'name' => 'Szymon',
    'surname' => 'Rykała',
    'password' => 'demo',
    'email' => 'szymonrykalaDemo@gmail.com',
    'action_key' => 'xxxyyy222333',
    'acces_id' => 10
);
$lastUserID = $user->create($userData);
echo "Last insert id: " . var_dump($lastUserID);

$foundItem = $user->search(array('name' => 'min'));
echo "List of found users: " . var_dump($foundItem);

$accesList = $user->read();
echo "List of users: " . var_dump($accesList);

$acces->delete($lastUserID);

