<?php
require_once __DIR__ . '/../../config/config.php';

$db = new Database();
$address = new Address($db);

/* {
    "number" => "7",
    "street" => "Grabkowska",
    "town" => "Kowal",
    "postal_code" => "87-820",
    "country" => "Poland"
} */
$addressData = array(
    'number' => "7",
    'street' => "Kołłątaja",
    'town' => "Kowal",
    'postal_code' => "87-720",
    'country' => "Poland"
);

echo "find with search method: ".var_dump($address->search(array('street'=>'skieg')));

if (!$address->exist(array($addressData))) {
    
    $insertIndex = $address->create($addressData);
    echo "Last index is $insertIndex \n";

    echo "List of addresses: " . var_dump($address->read());

    $address->update($insertIndex, array('postal_code' => "87-821"));

    echo "Update inserted address: " . var_dump($address->read(array('id' => $insertIndex)));

    $address->delete($insertIndex);
}
