<?php
namespace models;
use utils\DBInterface;

class Address extends Model
{
    protected string $tableName = 'addresses';
    public array $columns = ['id', 'country', 'town', 'postal_code', 'street', 'number'];

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
                default:
                    $value = (string) filter_var($value, FILTER_SANITIZE_STRING);
                    break;
            }
        }
    }

    public function create(array $data): int
    {
        if ($this->exist($data))
        {
            throw new HttpConflictException("$this->tableName with given data already exist. Data:" . json_encode($data));
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
