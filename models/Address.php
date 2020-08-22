<?php
require_once __DIR__ . '/Model.php';

class Address extends Model
{
    protected $tableName = 'addresses';
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
                    $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
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

        foreach ($data as $key => $value) {
            if (empty($value)) {
                throw new EmptyVariableException($key);
            }
        }

        if ($this->exist(array($data))) {
            throw new AlreadyExistException($data);
        }

        $this->DB->query(
            "INSERT INTO $this->tableName(number,town,street,postal_code,country)
                VALUES(:number,:town,:street,:postal_code,:country)",
            array(
                ':number' => $data['number'],
                ':street' => $data['street'],
                ':town' => $data['town'],
                ':postal_code' => $data['postal_code'],
                ':country' => $data['country']
            )
        );
        return $this->DB->lastInsertID();
    }
}
