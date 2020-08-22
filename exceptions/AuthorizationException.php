<?php

class AuthorizationException extends Exception
{
    public $httpCode = 401;
    public function __construct(string $reason)
    {
        $message = "Authorization error - $reason";
        $code = 3006;
        parent::__construct($message, $code);
    }
}
