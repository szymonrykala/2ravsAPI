<?php

namespace models;

use utils\types\MyBool;
use utils\types\MyString;
use utils\types\MyInt;

final class Reservation extends GenericModel
{
    protected string $tableName = 'reservations';
    protected array $SCHEMA = [
        'id' => [
            'type' => MyInt::class,
        ],
        'title' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/^[A-z\.\-\s\p{L}]{3,}$/u',
            'sanitize' => FILTER_SANITIZE_SPECIAL_CHARS
        ],
        'description' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'nullable' => true,
            'sanitize' => FILTER_SANITIZE_SPECIAL_CHARS
        ],
        'room_id' => [
            'type' => MyInt::class,
            'create' => true
        ],
        'building_id' => [
            'type' => MyInt::class,
            'create' => true
        ],
        'user_id' => [
            'type' => MyInt::class,
            'create' => true,
        ],
        'start_time' => [
            'type' => MyString::class,
            'pattern' => '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'create' => true,
            'update' => true
        ],
        'end_time' => [
            'type' => MyString::class,
            'pattern' => '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/',
            'create' => true,
            'update' => true
        ],
        'date' => [
            'type' => MyString::class,
            'pattern' => '/^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$/',
            'create' => true,
            'update' => true
        ],
        'confirmed' => [
            'type' => MyBool::class,
            'update' => true
        ],
        'confirming_user_id' => [
            'type' => MyInt::class,
            'update' => true,
            'default' => null,
            'nullable' => true
        ],
        'confirmed_at' => [
            'type' => MyBool::class,
            'default' => null,
            'nullable' => true
        ],
        'created' => [
            'type' => MyString::class,
        ],
        'updated' => [
            'type' => MyString::class,
        ]
    ];

    private function checkTimeSlot(array $data)
    {
        //checking time
        $Date = new \DateTime();

        $currentDate = $Date->format('Y-m-d');
        $currentTime = $Date->format('H:i:s');
        if ($data['date'] && $currentDate > $data['date']) {
            throw new HttpBadRequestException('Reservation date is too late');
        } elseif (
            $data['start_time'] && $data['date'] &&
            $currentDate == $data['date'] && $currentTime >= $data['start_time']
        ) throw new HttpBadRequestException('Reservation time is too late');


        if (
            $data['end_time'] && $data['start_time'] &&
            strtotime('+15 minutes', strtotime($data['start_time'])) >= strtotime($data['end_time'])
        ) throw new HttpBadRequestException('Reservation time is not correct. Start time have to be smaller then end time. Reservation time slot have to be at least 15 minutes');

        $result = $this->DB->query(
            'SELECT COUNT(id) AS `conflict` FROM `' . $this->tableName . '` WHERE 
               `room_id`=:room_id AND
                    (
                        `start_time` BETWEEN :start_time AND :end_time
                        OR
                        `end_time` BETWEEN :start_time AND :end_time
                        OR (`start_time`<:start_time AND `end_time`>:end_time)
                    ) AND `date`=:date',
            [
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time'],
                ':room_id' => $data['room_id'],
                ':date' => $data['date']
            ]
        )[0];

        if ((int) $result['conflict'] > 0) {
            throw new HttpConflictException('Given time slot is not accessible for given room. Room is reserved in time slot You specified.');
        }
    }


    public function update(array $data): void
    {
        //check time reservation - there can't be a collision
        $this->checkTimeSlot($data);
        //throws exception when time collision is occured

        parent::update($data);
    }

    public function create(array $data): int
    {
        //check time reservation - there can't be a collision
        $this->checkTimeSlot($data);
        //throws exception when time collision is occured

        return parent::create($data);
    }
}
