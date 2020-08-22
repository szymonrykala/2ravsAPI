<?php

class AlreadyExistException extends Exception
{
    public $httpCode = 409;
    public function __construct(array $givenData)
    {
        unset($givenData['password']);
        unset($givenData['action_key']);

        $message = "Resource with data: ";
        foreach ($givenData as $key => $value) {
            $message .= "'$key= $value', ";
        }
        $code = 2005;
        parent::__construct($message . "already exist", $code);
    }
}
