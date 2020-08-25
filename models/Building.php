<?php
require_once __DIR__ . '/Model.php';

class Building extends Model
{
    protected $tableName = 'buildings';
    public $unUpdateAble = array('id', 'user_id', 'created_at', 'confirmed_at');

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
        foreach ($data as $key => $value) {
            if (empty($value)) {
                throw new EmptyVariableException($key);
            }
        }

        if ($this->exist(array($data))) {
            throw new AlreadyExistException($data);
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
