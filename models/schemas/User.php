<?php

namespace models\schemas;

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
    'surname' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u'
    ],
    'password' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/.+/'
    ],
    'email' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => "/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/i"
    ],
    'activated' => [
        'type' => MyBool::class,
        'update' => true,
    ],
    'login_fails' => [
        'type' => MyInt::class,
        'update' => true,
    ],
    'action_key' => [
        'type' => MyString::class,
        'pattern' => '/.+/'
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
