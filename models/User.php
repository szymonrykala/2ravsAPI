<?php
require_once __DIR__ . '/Model.php';

class User extends Model
{
    protected $tableName = 'users';
    public $unUpdateAble = array('id', 'created_at');

    public function __construct(DBInterface $db)
    {
        parent::__construct($db);
    }

    public function parseData(array $data): array
    {
        foreach ($data as $key => &$value) {
            switch ($key) {
                case 'id':
                    $value = (int) $value;
                    break;
                case 'acces_id':
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
        return $data;
    }

    public function create(array $data): int
    {
        $data = $this->parseData($data);

        $data['name'] = preg_replace('/\s/', '', $data['name']);
        $data['surname'] = preg_replace('/\s/', '', $data['surname']);

        //checking is it empty
        foreach ($data as $key => $value) {
            if (empty($value) && $key !== 'activated') {
                throw new EmptyVariableException($key);
            }
        }

        if ($this->exist(array('email' => $data['email']))) {
            throw new AlreadyExistException($data);
        }

        $options = [
            'cost' => 12,
        ];
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, $options);

        $this->DB->query(
            "INSERT INTO 
                $this->tableName(name,surname,password,email,action_key,img_url)
               VALUES(:name,:surname,:password,:email,:action_key,:img_url)",
            array(
                ':name' => $data['name'],
                ':surname' => $data['surname'],
                ':password' => $data['password'],
                ':email' => $data['email'],
                ':action_key' => $data['action_key'],
                ':img_url' => ROOT . "/img/users/default.jpg"
            )
        );
        return $this->DB->lastInsertID();
    }
}
