<?php
require_once __DIR__ . '/Model.php';

class Building extends Model
{
    /**
     * Responsible for operation with buildings table in database
     */
    protected $tableName = 'buildings';
    public $unUpdateAble = array('id');
    public $columns = ['id', 'name', 'rooms_count', 'address_id'];

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
            switch ($key) {
                case 'name':
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                    break;
                default:
                    $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
            }
        }
    }

    public function create(array $data): int
    {
        /**
         * Creating new Building in database 
         * 
         * @param array $data array with params:name
         * @return int inserted item index
         */
        if ($this->exist($data))
        {
            throw new InvalidArgumentException("$this->tableName with given data already exist. Data:" . json_encode($data), 400);
        }

        $this->DB->query(
            "INSERT INTO $this->tableName(address_id,name,rooms_count) VALUES(:address_id,:name,:rooms_count)",
            array(
                ':address_id' => $data['address_id'],
                ':name' => $data['name'],
                ':rooms_count' => $data['rooms_count'],
            )
        );
        return $this->DB->lastInsertID();
    }
}
