<?php
namespace models;
use utils\DBInterface;

class Reservation extends Model
{
    protected string $tableName = 'reservations';
    protected array $columns = [
        'id', 'title', 'subtitle', 'room_id', 'building_id', 'user_id',
        'start_time', 'end_time', 'date', 'created_at', 'updated_at',
        'confirmed', 'confirming_user_id', 'confirmed_at', 'deleted'
    ];

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array &$data): void
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
    }

    private function checkTimeSlot(string $startTime, string $endTime, string $date, int $roomID)
    {
        if (strtotime('+15 minutes', strtotime($startTime)) >= strtotime($endTime)) {
            throw new HttpBadRequestException("Reservation time is not correct. Start time have to be smaller then end time. Reservation time slot have to be at least 15 minutes");
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
            throw new HttpConflictException("Given time slot is not accessible for given room. Room is reserved in time slot You specified.");
        }
    }

    public function create(array $data): int
    {
        //checking time
        $Date = new \DateTime();

        $currentDate = $Date->format('Y-m-d');
        $currentTime = $Date->format('H:i:s');
        if ($currentDate > $data['date']) {
            throw new HttpBadRequestException("Reservation date is too late");
        } elseif ($currentDate == $data['date'] && $currentTime >= $data['start_time']) {
            throw new HttpBadRequestException("Reservation time is too late");
        }

        //building exist?
        $buildingExist = $this->DB->query(
            "SELECT id FROM buildings WHERE id=:id",
            array(':id' => $data['building_id'])
        );
        if (empty($buildingExist)) { //if not exist
            throw new HttpNotFoundException("Specified building is not exist. You can not make reservation because building You specified is not Exsist");
        }

        //room exist in this building?
        $room = $this->DB->query(
            "SELECT id,blockade FROM rooms WHERE id=:room_id AND building_id=:building_id",
            array(
                ':room_id' => $data['room_id'],
                ':building_id' => $data['building_id']
            )
        );
        if (empty($room)) { //if not exist
            throw new HttpNotFoundException("Specified room is not exist. You can not make reservation because specified room is not exist in given building");
        } else {
            //room is bookable?
            if ((bool)$room[0]['blockade']) {
                throw new HttpConflictException("Specified room is not bookable. Room You want to reserve has blocked status."); //conflict
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
            [
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
            ]
        );
        return $this->DB->lastInsertID();
    }
}
