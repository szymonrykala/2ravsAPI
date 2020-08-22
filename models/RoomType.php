<?php
require_once __DIR__ . '/Model.php';

class RoomType extends Model
{
    protected $tableName = 'room_types';
    public $unUpdateAble = array('id');

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array $data): array
    {
        foreach ($data as $key => &$value) {
            switch ($key) {
                case 'id':
                    $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                default:
                    $value = (string) filter_var($value, FILTER_SANITIZE_STRING);
                    break;
            }
        }
        return $data;
    }

    public function create(array $data): int
    {
        $data = $this->parseData($data);

        if (empty($data['name'])) {
            throw new EmptyVariableException('name');
        }

        $this->Database->query(
            "INSERT INTO $this->tableName(name) VALUES(:name)",
            array(':name' => $data['name'])
        );
        return $this->DB->lastInsertID();
    }
}
