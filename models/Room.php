<?php

namespace models;

use utils\types\MyArray;
use utils\types\MyString;
use utils\types\MyInt;
use utils\types\MyBool;

class Room extends Model
{
    protected string $tableName = 'rooms';
    protected array $SCHEMA= [
        'id' => [
            'type' => MyInt::class,
        ],
        'name' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            // 'pattern' => '/^[\.\s0-9\wa-zżźćńółęąśŻŹĆĄŚĘŁÓŃ]{3,}$/u'
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ],
        'rfid' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/[\w]+/u'
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
            'type' => MyString::class,
            'pattern' => '/.+/'
        ],
        'updated' => [
            'type' => MyString::class,
            'pattern' => '/.+/'
        ]
    ];
}
