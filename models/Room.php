<?php

namespace models;

use utils\DBInterface;

class Room extends Model
{
    protected string $tableName = 'rooms';
    public array $columns = [
        'id', 'name', 'rfid', 'building_id', 'room_type_id', 'seats_count', 'floor',
        'equipment', 'blockade', 'state'
    ];

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array &$data): void
    {
        foreach ($data as $key => &$value) {
            switch ($key) {
                case 'name':
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                    break;
                case 'rfid':
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                    break;
                case 'equipment':
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                    break;
                case 'blockade':
                    $value = (bool) $value;
                    break;
                case 'state':
                    $value = (bool) $value;
                    break;
                default:
                    $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
            }
        }
    }

    public function create(array $data): int
    {
        if ($this->exist([
            "name" => $data["name"],
            "floor" => $data["floor"],
            "building_id" => $data["building_id"]
        ])) {
            throw new HttpConflictException("$this->tableName with given data already exist. Data:" . json_encode($data));
        }

        $this->DB->query(
            "INSERT INTO $this->tableName(building_id,name,state,floor,room_type_id,seats_count,equipment,rfid,blockade)
                         VALUES(:building_id,:name,0,:floor,:room_type_id,:seats_count,:equipment,:rfid,:blockade)",
            array(
                ':name' => $data['name'],
                ':floor' => $data['floor'],
                ':room_type_id' => $data['room_type_id'],
                ':seats_count' => $data['seats_count'],
                ':equipment' => $data['equipment'],
                ':building_id' => $data['building_id'],
                ':rfid' => $data['rfid'],
                ':blockade' => $data['blockade']
            )
        );
        return $this->DB->lastInsertID();
    }
}
