<?php

namespace models\schemas;

use utils\types\MyInt;
use utils\types\MyString;

return [
    'id' => [
        'type' => MyInt::class,
    ],
    'message' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/.*/'
    ],
    'user_id' => [
        'type' => MyInt::class,
        'create' => true,
    ],
    'building_id' => [
        'type' => MyInt::class,
        'default' => Null,
        'create' => true,
        'nullable' => true
    ],
    'room_id' => [
        'type' => MyInt::class,
        'default' => Null,
        'create' => true,
        'nullable' => true
    ],
    'reservation_id' => [
        'type' => MyInt::class,
        'default' => Null,
        'create' => true,
        'nullable' => true
    ],
    'created' => [
        'type' => MyString::class,
        'pattern' => '/.+/'
    ]
];
