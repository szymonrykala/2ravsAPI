<?php

namespace models;

use utils\types\MyInt;
use utils\types\MyString;

final class Log extends GenericModel
{
    protected string $tableName = 'logs';
    protected array $SCHEMA = [
        'id' => [
            'type' => MyInt::class,
        ],
        'message' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'sanitize' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ],
        'user_id' => [
            'type' => MyInt::class,
            'create' => true,
        ],
        'building_id' => [
            'type' => MyInt::class,
            'default' => Null,
            'create' => true,
            'nullable' => true
        ],
        'room_id' => [
            'type' => MyInt::class,
            'default' => Null,
            'create' => true,
            'nullable' => true
        ],
        'reservation_id' => [
            'type' => MyInt::class,
            'default' => Null,
            'create' => true,
            'nullable' => true
        ],
        'created' => [
            'type' => MyString::class,
        ]
    ];

    public function create(array $createData): int
    {
        $this->DB->query(
            'INSERT INTO `' . $this->tableName . '`(`message`,`user_id`,`reservation_id`,`building_id`,`room_id`)
            VALUES(:message,:user_id,:reservation_id,:building_id,:room_id)',
            [
                ':message' => $createData['message'],
                ':user_id' => $createData['user_id'],
                ':reservation_id' => $createData['reservation_id'] ?? Null,
                ':building_id' => $createData['building_id'] ?? Null,
                ':room_id' => $createData['room_id'] ?? Null,
            ]
        );
        return $this->DB->lastInsertID();
    }
}
