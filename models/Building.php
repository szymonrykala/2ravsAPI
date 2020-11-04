<?php

namespace models;

use utils\types\MyInt;
use utils\types\MyString;

final class Building extends GenericModel
{
    /**
     * Responsible for operation with buildings table in database
     */
    protected string $tableName = 'buildings';
    protected array $SCHEMA = [
        'id' => [
            'type' => MyInt::class
        ],
        'name' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/^[0-9A-z\.\-\s\p{L}]{3,}$/u',
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
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
}
