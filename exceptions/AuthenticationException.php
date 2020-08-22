<?php

class AuthenticationException extends Exception
{
    public $httpCode = 401;
    public function __construct(string $wrongCredencial='')
    {

        $message = "User with given credencial $wrongCredencial does not exist";
        $code = 3003;
        parent::__construct($message,$code);
    }
}
