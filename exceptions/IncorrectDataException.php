<?php

class IncorrectDataException extends Exception
{
    public $httpCode = 400;
    public function __construct(string $variableName)
    {
        $this->source = $variableName;
        $message = "Nothing found in $variableName with given parameters";
        $code = 3001;
        parent::__construct($message,$code);
    }
}
