<?php
require_once __DIR__ . '/Model.php';

class Log extends Model
{
    protected $tableName = 'logs';
    public $unUpdateAble = array('id', 'created_at', 'building_id', 'room_id', 'reservation_id');

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array $data): array
    {
        foreach ($data as $key => &$value) {
            switch ($key) {
                case "message":
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                    break;
                case "created_at":
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                    break;
                default:
                    $value = (int) $value;
                    break;
            }
        }
        return $data;
    }

    public function create(array $data): int
    {

        $data = $this->parseData($data);

        //checking is it empty
        foreach ($data as $key => &$value) {
            if (empty($value)) {
                throw new EmptyVariableException($key);
            }
            if (in_array($key, $this->unUpdateAble)) {
                throw new UnUpdateableParameterException($key);
            }
        }

        // have to fill not existing fields
        $fields = array('room_id', 'reservation_id', 'building_id');
        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                $data[$field] = null;
            }
        }

        $this->DB->query(
            "INSERT INTO $this->tableName(
                message,
                user_id,
                room_id,
                building_id,
                reservation_id
            ) 
            VALUES(
                :message,
                :user_id,
                :room_id,
                :building_id,
                :reservation_id
            )",
            array(
                ':message' => $data['message'],
                ':user_id' => $data['user_id'],
                ':room_id' => $data['room_id'],
                ':building_id' => $data['building_id'],
                ':reservation_id' => $data['reservation_id']
            )
        );

        return $this->DB->lastInsertID();
    }
}
