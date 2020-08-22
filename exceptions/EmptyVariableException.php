<?php

class EmptyVariableException extends Exception
{
    public $httpCode = 400;
    public function __construct(string $variableName)
    {
        $this->source = $variableName;
        $message = "Given variable '$variableName' can not be empty.";
        $code = 2003;
        parent::__construct($message, $code);
    }
}
