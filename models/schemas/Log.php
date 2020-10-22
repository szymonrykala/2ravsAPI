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
        'pattern' => '/^[A-z\.\-\s\p{L}]+$/u'
    ],
    'user_id' => [
        'type' => MyInt::class
    ],
    'building_id' => [
        'type' => MyInt::class,
        'nullable' => true
    ],
    'room_id' => [
        'type' => MyInt::class,
        'nullable' => true
    ],
    'reservation_id' => [
        'type' => MyInt::class,
        'nullable' => true
    ],
    'created' => [
        'type' => MyString::class,
        'pattern' => '/.+/'
    ]
];
