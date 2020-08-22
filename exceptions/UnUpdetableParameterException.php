<?php

class UnUpdateableParameterException extends Exception
{
    public $httpCode = 400;
    public function __construct(string $parameter)
    {
        $this->source = $parameter;
        $message = "Given parameter $parameter can not be update";
        $code = 2002;
        parent::__construct($message, $code);
    }
}
