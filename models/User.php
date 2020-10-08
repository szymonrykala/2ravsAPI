<?php
require_once __DIR__ . '/Model.php';

class User extends Model
{
    protected $tableName = 'users';
    public $unUpdateAble = array('id', 'created_at');
    protected $columns = [
        'id', 'access_id', 'name', 'surname', 'password',
        'last_login', 'email', 'updated_at',
        'activated', 'login_fails', 'created_at', 'action_key'
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
                    $value = (int) $value;
                    break;
                case 'access_id':
                    $value = (int) $value;
                    break;
                case 'login_fails':
                    $value = (int) $value;
                    break;
                case 'activated':
                    $value = (bool) $value;
                    break;
                default:
                    $value = filter_var($value, FILTER_SANITIZE_STRING);
                    break;
            }
        }
    }

    public function create(array $data): int
    {
        $data['name'] = preg_replace('/\s/', '', $data['name']);
        $data['surname'] = preg_replace('/\s/', '', $data['surname']);

        if ($this->exist(['email' => $data['email']])) {
            throw new InvalidArgumentException("$this->tableName with given email already exist.", 400);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $this->DB->query(
            "INSERT INTO 
                $this->tableName(name,surname,password,email,action_key)
               VALUES(:name,:surname,:password,:email,:action_key)",
            array(
                ':name' => $data['name'],
                ':surname' => $data['surname'],
                ':password' => $data['password'],
                ':email' => $data['email'],
                ':action_key' => $data['action_key']
            )
        );
        return $this->DB->lastInsertID();
    }
}
