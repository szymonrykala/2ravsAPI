<?php

class APIException extends Exception
{
    public $httpCode = 500;
    public function __construct(string $message,int $code)
    {
        $this->httpCode = $code;
        parent::__construct($message, $code);
    }
}