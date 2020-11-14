<?php

namespace models;

use utils\types\MyInt;
use utils\types\MyString;

final class Address extends GenericModel
{
    protected string $tableName = 'addresses';
    protected array $SCHEMA= [
        'id' => [
            'type' => MyInt::class,
        ],
        'country' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u',
            'sanitize' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ],
        'town' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u',
            'sanitize' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
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
            'pattern' => '/[A-z\.\-\s\p{L}]{3,}/u',
            'sanitize' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ],
        'number' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/^\d+[A-z]?(\/\d+[A-z]?)?$/'
        ],
        'created' => [
            'type' => MyString::class
        ],
        'updated' => [
            'type' => MyString::class
        ]
    ];
}
