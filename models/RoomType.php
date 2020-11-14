<?php

namespace models;

use utils\types\MyInt;
use utils\types\MyString;

final class RoomType extends GenericModel
{
    /**
     * Responsible for operation with room_types table in database
     */
    protected string $tableName = 'room_types';
    protected array $SCHEMA= [
        'id' => [
            'type' => MyInt::class,
        ],
        'name' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u',
            'sanitize' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ],
        'created' => [
            'type' => MyString::class,
        ],
        'updated' => [
            'type' => MyString::class,
        ]
    ];
}
