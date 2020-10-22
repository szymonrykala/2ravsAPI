<?php

namespace models\schemas;

use utils\types\MyArray;
use utils\types\MyString;
use utils\types\MyInt;
use utils\types\MyBool;

return [
    'id' => [
        'type' => MyInt::class,
    ],
    'name' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u'
    ],
    'rfid' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/[\w\.-\s]+/u'
    ],
    'building_id' => [
        'type' => MyInt::class,
        'create' => true,
        'update' => true,
    ],
    'room_type_id' => [
        'type' => MyInt::class,
        'create' => true,
        'update' => true,
    ],
    'seats_count' => [
        'type' => MyInt::class,
        'create' => true,
        'update' => true,
    ],
    'floor' => [
        'type' => MyInt::class,
        'create' => true,
        'update' => true,
    ],
    'equipment' => [
        'type' => MyArray::class,
        'create' => true,
        'update' => true,
        'nullable' => true
    ],
    'blockade' => [
        'type' => MyBool::class,
        'update' => true,
    ],
    'occupied' => [
        'type' => MyBool::class,
        'update' => true,
    ],
    'created' => [
        'type' => MyString::class,
        'pattern' => '/.+/'
    ],
    'updated' => [
        'type' => MyString::class,
        'pattern' => '/.+/'
    ]
];
