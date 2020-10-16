<?php
require_once __DIR__ . '/Model.php';

class Access extends Model
{
    protected $tableName = 'accesses';
    public $unUpdateAble = array('id');
    protected $columns = [
        'id', 'name','rfid_action', 'access_edit', 'buildings_view',
        'buildings_edit', 'logs_view', 'logs_edit', 'rooms_view', 'rooms_edit',
        'reservations_access', 'reservations_confirm', 'reservations_edit',
        'users_edit', 'statistics_view'
    ];

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array &$data):void
    {
        foreach ($data as $key => &$value) {
            switch ($key) {
                case 'name':
                    $value = (string) filter_var($value, FILTER_SANITIZE_STRING);
                    break;
                case 'id':
                    $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                default:
                    $value = (bool) $value;
                    break;
            }
        }
    }

    public function create(array $data): int
    {
        if ($this->exist($data)) throw new InvalidArgumentException("$this->tableName with given data already exist. Data:" . json_encode($data), 400);
    
        $this->DB->query(
            "INSERT INTO $this->tableName(
                  name,
                  acces_edit,
                  logs_edit, logs_view,
                  reservations_confirm, reservations_acces,reservations_edit,
                  rooms_edit,rooms_view,
                  buildings_edit,buildings_view,
                  statistics_view,
                  users_edit
             )
             VALUES(
                  :name,
                  :acces_edit,
                  :logs_edit, :logs_view,
                  :reservations_confirm, :reservations_acces,:reservations_edit,
                  :rooms_edit,:rooms_view,
                  :buildings_edit,:buildings_view,
                  :statistics_view,
                  :users_edit
             )",
            array(
                ':name' => $data['name'],
                ':acces_edit' => $data['acces_edit'],
                ':logs_edit' => $data['logs_edit'],
                ':logs_view' => $data['logs_view'],
                ':reservations_confirm' => $data['reservations_confirm'],
                ':reservations_acces' => $data['reservations_acces'],
                ':reservations_edit' => $data['reservations_edit'],
                ':rooms_edit' => $data['rooms_edit'],
                ':rooms_view' => $data['rooms_view'],
                ':buildings_edit' => $data['buildings_edit'],
                ':buildings_view' => $data['buildings_view'],
                ':statistics_view' => $data['statistics_view'],
                ':users_edit' => $data['users_edit']
            )
        );
        return $this->DB->lastInsertID();
    }
}
