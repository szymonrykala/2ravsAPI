<?php

namespace models\schemas;

use utils\types\MyString;
use utils\types\MyInt;
use utils\types\MyBool;

return [
    'id' => [
        'type' => MyInt::class,
    ],
    'access_id'=>[
        'type' => MyInt::class,
    ],
    'name' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/^[A-Za-zżźćńółęąśŻŹĆĄŚĘŁÓŃ]{3,}$/',
        'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ],
    'surname' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/^[A-Za-zżźćńółęąśŻŹĆĄŚĘŁÓŃ]{3,}$/',
        'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ],
    'password' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/(?=.{8,})(?=.*[!@#$%^&*])(?=.*[0-9]{2,})(?=.*[A-Z])/'
    ],
    'last_login' => [
        'type' => MyString::class,
    ],
    'email' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/.+{5,}/',
        'filter' => FILTER_SANITIZE_EMAIL,
        'validate' => FILTER_VALIDATE_EMAIL
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
        'create' => true,
        'update' => true,
        'pattern' => '/\w+/'
    ],
    'created' => [
        'type' => MyString::class,
        // 'pattern' => '/.+/'
    ],
    'updated' => [
        'type' => MyString::class,
        'pattern' => '/.+/'
    ]
];
