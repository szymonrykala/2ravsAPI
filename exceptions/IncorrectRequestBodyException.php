<?php

class IncorrectRequestBodyException extends Exception
{
    public $httpCode = 400;
    public function __construct()
    {
        $message = "No Data has been passed or incorrect data format";
        $code = 3001;
        parent::__construct($message,$code);
    }
}
