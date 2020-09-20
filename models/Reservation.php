<?php
require_once __DIR__ . '/Model.php';

class Reservation extends Model
{
    protected $tableName = 'reservations';
    public $unUpdateAble = array('id', 'room_id', 'building_id', 'user_id', 'created-at');
    protected $columns = [
        'id', 'title', 'subtitle', 'room_id', 'building_id', 'user_id',
        'start_time', 'end_time', 'date', 'created_at', 'updated_at',
        'confirmed', 'confirming_user_id', 'confirmed_at', 'deleted'
    ];

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array $data): array
    {
        foreach ($data as $key => &$value) {
            switch ($key) {
                case 'id':
                    $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'room_id':
                    $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'building_id':
                    $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'user_id':
                    $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'confirming_user_id':
                    $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'confirmed':
                    $value = (bool) $value;
                    break;
                case 'deleted':
                    $value = (bool) $value;
                    break;
                default:
                    $value = (string) filter_var($value, FILTER_SANITIZE_STRING);
                    break;
            }
        }
        return $data;
    }

    private function checkTimeSlot(string $startTime, string $endTime, string $date, int $roomID)
    {
        $explodedEndTime = explode(':', $endTime);
        $explodedStartTime = explode(':', $startTime);
        if ($explodedStartTime[0] > $explodedEndTime[0]) {
            throw new Exception("Reservation time is not correct. Start time have to be smaller then end time", 400);
        }
        $result = $this->DB->query(
            "SELECT COUNT(id) AS 'conflict' FROM $this->tableName WHERE 
               room_id=:room_id AND deleted=:deleted AND
                    (
                         start_time BETWEEN :start_time AND :end_time
                         OR
                         end_time BETWEEN :start_time AND :end_time
                         OR (start_time<:start_time AND end_time>:end_time)
                    ) AND date=:date",
            array(
                ':start_time' => $startTime,
                ':end_time' => $endTime,
                ':room_id' => $roomID,
                ':date' => $date,
                ':deleted' => false
            )
        )[0];

        if ((int) $result['conflict'] > 0) {
            throw new Exception("Given time slot is not accessible for given room. Room is reserved in time slot You specified.", 409);
        }
    }

    public function create(array $data): int
    {
        $data = $this->filterVariables($data);
        $data = $this->parseData($data);

        //checking time
        $Date = new DateTime();

        $currentDate = $Date->format('Y-m-d');
        $currentTime = $Date->format('H:i:s');
        if ($currentDate > $data['date']) {
            throw new Exception("Reservation date is too late", 400);
        } elseif ($currentDate == $data['date'] && $currentTime >= $data['start_time']) {
            throw new Exception("Reservation time is too late", 400);
        }

        //building exist?
        $buildingExist = $this->DB->query(
            "SELECT id FROM buildings WHERE id=:id",
            array(':id' => $data['building_id'])
        );
        if (empty($buildingExist)) { //if not exist
            throw new Exception("Specified building is not exist. You can not make reservation because building You specified is not Exsist", 400);
        }

        //room exist in this building?
        $roomExist = $this->DB->query(
            "SELECT id,blockade FROM rooms WHERE id=:room_id AND building_id=:building_id",
            array(
                ':room_id' => $data['room_id'],
                ':building_id' => $data['building_id']
            )
        );
        if (empty($roomExist)) { //if not exist
            throw new Exception("Specified room is not exist. You can not make reservation because specified room is not exist in given building", 400);
        } else {
            //room is bookable?
            if ((bool)$roomExist[0]['blockade']) {
                throw new Exception("Specified room is not bookable. Room You want to reserve has blocked status.", 409); //conflict
            }
        }


        //check time reservation - there can't be a collision
        $this->checkTimeSlot($data['start_time'], $data['end_time'], $data['date'], $data['room_id']);
        //throws exception when time collision is occured

        $this->DB->query(
            "INSERT INTO $this->tableName(
                    building_id,confirmed,confirming_user_id,confirmed_at,
                    created_at,end_time,room_id,start_time,date,subtitle,title,user_id
               ) VALUES(
                    :building_id,:confirmed,:confirming_user_id,:confirmed_at,
                    NOW(),:end_time,:room_id,:start_time,:date,:subtitle,:title,:user_id
               )",
            array(
                ':user_id' => $data['user_id'],
                ':building_id' => $data['building_id'],
                ':room_id' => $data['room_id'],
                ':confirmed' => false,
                ':confirmed_at' => null,
                ':confirming_user_id' => null,
                ':title' => $data['title'],
                ':subtitle' => $data['subtitle'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time'],
                ':date' => $data['date']
            )
        );
        return $this->DB->lastInsertID();
    }
}
