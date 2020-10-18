<?php
namespace models;
use utils\DBInterface;

class Building extends Model
{
    /**
     * Responsible for operation with buildings table in database
     */
    protected string $tableName = 'buildings';
    public array $columns = ['id', 'name', 'rooms_count', 'address_id'];

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
            throw new HttpConflictException("$this->tableName with given data already exist. Data:" . json_encode($data));
        }

        $this->DB->query(
            "INSERT INTO $this->tableName(address_id,name) VALUES(:address_id,:name)",
            array(
                ':address_id' => $data['address_id'],
                ':name' => $data['name']
            )
        );
        return $this->DB->lastInsertID();
    }
}
