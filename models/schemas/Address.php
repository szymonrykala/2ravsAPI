<?php

namespace models\schemas;

use utils\types\MyInt;
use utils\types\MyString;

return [
    'id' => [
        'type' => MyInt::class,
    ],
    'country' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u'
    ],
    'town' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u'
    ],
    'postal_code' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/^\d{2}-\d{3}$/'
    ],
    'street' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/[A-z\.\-\s\p{L}]{3,}/u'
    ],
    'number' => [
        'type' => MyString::class,
        'create' => true,
        'update' => true,
        'pattern' => '/^\d+[A-z]?(\/\d+[A-z]?)?$/'
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
