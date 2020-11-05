<?php

namespace models;

use utils\types\MyString;
use utils\types\MyInt;
use utils\types\MyBool;

final class User extends GenericModel
{
    protected string $tableName = 'users';
    protected array $SCHEMA = [
        'id' => [
            'type' => MyInt::class,
        ],
        'access_id' => [
            'type' => MyInt::class
        ],
        'name' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/^[A-Za-zżźćńółęąśŻŹĆĄŚĘŁÓŃ]{3,}$/',
            'sanitize' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ],
        'surname' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/^[A-Za-zżźćńółęąśŻŹĆĄŚĘŁÓŃ]{3,}$/',
            'sanitize' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ],
        'password' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/(?=.{8,})(?=.*[!@#$%^&*])(?=.*[0-9]{2,})(?=.*[A-Z])/'
        ],
        'last_login' => [
            'type' => MyString::class,
        ],
        'email' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'sanitize' => FILTER_SANITIZE_EMAIL,
            'validate' => FILTER_VALIDATE_EMAIL
        ],
        'activated' => [
            'type' => MyBool::class,
            'update' => true,
        ],
        'login_fails' => [
            'type' => MyInt::class,
            'update' => true,
        ],
        'action_key' => [
            'type' => MyString::class,
            'create' => true,
            'update' => true,
            'pattern' => '/\w+/'
        ],
        'created' => [
            'type' => MyString::class
        ],
        'updated' => [
            'type' => MyString::class
        ]
    ];

    public function login(string $email, string $password): void
    {
        /**
         * Login the user
         */

        // $this->setID($this->data['id']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new HttpBadRequestException('Given email has incorrect format.');
        }

        $this->data = $this->read(['email' => $email])[0];
        $this->id = $this->data['id'];

        if ($this->data['login_fails'] >= 5) {
            throw new HttpForbiddenException('Can not login. Login failed to many times and Your account is locked. Please contact with Your administrator');
        }

        if ($this->data['activated'] === false) {
            throw new HttpConflictException("Can not authenticate because user is not activated");
        }

        if (!password_verify($password, $this->data['password'])) {
            $this->data['login_fails'] += 1;
            $this->update(['login_fails' => $this->data['login_fails']]);
            throw new HttpBadRequestException('Authentication failed (count:' . $this->data['login_fails'] . '). Password is not correct.');
        }
    }

    public function register(array $data): int
    {
        // checking password policies
        $this->fieldCreatePolicy('password', $data);

        // hashing the password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        // unset checking password with regex pattern becouse of it has been already checked 
        // now it is a hash so it can not be validated with defined rules
        unset($this->SCHEMA['password']['pattern']);

        $this->create($data);
        return $this->DB->lastInsertID();
    }
}
