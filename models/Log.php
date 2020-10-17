<?php
require_once __DIR__ . '/Model.php';

class Log extends Model
{
    protected string $tableName = 'logs';
    public array $columns = ['id', 'message', 'created_at', 'user_id', 'building_id', 'room_id', 'reservation_id'];

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array &$data): void
    {
        foreach ($data as $key => &$value) {
            if ($value === null) {
                continue;
            }
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
    }

    public function create(array $data): int
    {
        foreach ($this->columns as $key) {
            if (!isset($data[$key])) {
                $data[$key] = Null;
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
