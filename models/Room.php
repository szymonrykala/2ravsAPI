<?php

namespace models;

use utils\types\MyArray;
use utils\types\MyString;
use utils\types\MyInt;
use utils\types\MyBool;

final class Room extends GenericModel
{
    protected string $tableName = 'rooms';
    protected array $SCHEMA = [
        'id' => [
            'type' => MyInt::class,
        ],
        'name' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'sanitize' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ],
        'rfid' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/[\w]+/'
        ],
        'building_id' => [
            'type' => MyInt::class,
            'create' => true,
            'update' => true,
        ],
        'room_type_id' => [
            'type' => MyInt::class,
            'create' => true,
            'update' => true,
        ],
        'seats_count' => [
            'type' => MyInt::class,
            'create' => true,
            'update' => true,
        ],
        'floor' => [
            'type' => MyInt::class,
            'create' => true,
            'update' => true,
        ],
        'equipment' => [
            'type' => MyArray::class,
            'create' => true,
            'update' => true,
            'nullable' => true
        ],
        'blockade' => [
            'type' => MyBool::class,
            'update' => true,
        ],
        'occupied' => [
            'type' => MyBool::class,
            'update' => true,
        ],
        'created' => [
            'type' => MyString::class
        ],
        'updated' => [
            'type' => MyString::class
        ]
    ];
}
