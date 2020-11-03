<?php
namespace models;

use utils\types\MyBool;
use utils\types\MyString;
use utils\types\MyInt;

class Access extends Model
{
    protected string $tableName = 'accesses';
    protected array $SCHEMA= [
        'id' => [
            'type' => MyInt::class,
        ],
        'name' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u',
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ],
        'rfid_action' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'access_edit' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'buildings_view' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'buildings_edit' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'logs_view' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'logs_edit' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'rooms_view' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'rooms_edit' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'reservations_access' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'reservations_confirm' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'reservations_edit' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'users_edit' => [
            'type' => MyBool::class,
            'create' => true,
            'update' => true,
        ],
        'statistics_view' => [
            'type' => MyBool::class,
            'create' => true,
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
