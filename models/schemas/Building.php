<?php

namespace models\schemas;

use utils\types\MyString;
use utils\types\MyInt;

return [
    'id' => [
        'type' => MyInt::class
    ],
    'name' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u'
    ],
    'rooms_count' => [
        'type' => MyInt::class
    ],
    'address_id' => [
        'type' => MyInt::class,
        'create' => true,
        'update' => true
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
