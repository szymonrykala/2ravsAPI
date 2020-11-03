<?php

namespace models\schemas;

use utils\types\MyBool;
use utils\types\MyString;
use utils\types\MyInt;

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
    'description' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/.{3,}/u',
        'nullable'=>true
    ],
    'room_id' => [
        'type' => MyInt::class,
        'create' => true,
        'update' => true,
    ],
    'building_id' => [
        'type' => MyInt::class,
        'create' => true,
        'update' => true,
    ],
    'user_id' => [
        'type' => MyInt::class,
        'create' => true,
    ],
    'start_time' => [
        'type' => MyString::class,
        'pattern' => '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
        'create' => true,
        'update' => true
    ],
    'end_time' => [
        'type' => MyString::class,
        'pattern' => '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
        'create' => true,
        'update' => true
    ],
    'date' => [
        'type' => MyString::class,
        'pattern' => '/^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$/',
        'create' => true,
        'update' => true
    ],
    'confirmed' => [
        'type' => MyBool::class,
        'update' => true
    ],
    'confirming_user_id' => [
        'type' => MyInt::class,
        'update' => true,
        'nullable'=>true
    ],
    'confirmed_at' => [
        'type' => MyBool::class,
        'pattern' => '/.+/',
        'nullable'=>true
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
