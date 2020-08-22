<?php

class NotExistException extends Exception
{
    public $httpCode = 404;
    public function __construct(string $thing, string $dependence = '')
    {
        $message = "Given $thing is not exist";
        $message .= $dependence !== '' ? " in given $dependence" : '';
        $code = 2007;
        parent::__construct($message, $code);
    }
}
