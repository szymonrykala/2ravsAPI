<?php
require_once __DIR__ . '/Model.php';

class RoomType extends Model
{
    /**
     * Responsible for operation with room_types table in database
     */
    protected string $tableName = 'room_types';
    public array $columns = ['id', 'name'];

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array &$data): void
    {
        /**
         * Used for parsing data to right data type
         * 
         * @param array $data 
         * @return array $data
         */
        foreach ($data as $key => &$value) {
            if (!in_array($key, $this->columns)) {
                unset($data[$key]);
                continue;
            }
            switch ($key) {
                case 'id':
                    $value =(int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                default:
                    $value = (string) filter_var($value, FILTER_SANITIZE_STRING);
                    break;
            }
        }
    }

    public function create(array $data): int
    {
        /**
         * Creating new room type in database 
         * 
         * @param array $data array with params:name
         * @return int inserted item index
         */

        if ($this->exist($data))
        {
            throw new InvalidArgumentException("$this->tableName with given data already exist. Data:" . json_encode($data), 400);
        }

        $this->DB->query(
            "INSERT INTO $this->tableName(name) VALUES(:name)",
            array(':name' => $data['name'])
        );
        return $this->DB->lastInsertID();
    }
}
