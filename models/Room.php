<?php
require_once __DIR__ . '/Model.php';

class Room extends Model
{
    protected $tableName = 'rooms';
    public $unUpdateAble = array('id');

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array $data): array
    {
        foreach ($data as $key => &$value) {
            switch ($key) {
                case 'name':
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
        return $data;
    }

    public function create(array $data): int
    {
        $data = $this->parseData($data);
        //checking is it empty
        foreach ($data as $key => $value) {
            if (empty($value) && $key !== 'state' && $key !== "floor") {
                throw new EmptyVariableException($key);
            }
        }

        if ($this->exist(array(
            "name" => $data["name"],
            "floor" => $data["floor"],
            "building_id" => $data["building_id"]
        ))) {
            throw new AlreadyExistException($data);
        }

        $this->DB->query(
            "INSERT INTO $this->tableName(building_id,name,state,floor,room_type_id,seats_count,equipment)
                         VALUES(:building_id,:name,0,:floor,:room_type_id,:seats_count,:equipment)",
            array(
                ':name' => $data['name'],
                ':floor' => $data['floor'],
                ':room_type_id' => $data['room_type_id'],
                ':seats_count' => $data['seats_count'],
                ':equipment' => $data['equipment'],
                ':building_id' => $data['building_id']
            )
        );
        return $this->DB->lastInsertID();
    }
}
