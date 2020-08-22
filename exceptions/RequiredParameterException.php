<?php

class RequiredParameterException extends Exception
{
    public $httpCode = 400;
    public function __construct(array $requiredParameters)
    {
        $params = "";
        foreach ($requiredParameters as $param=>$value) {
            $params .= "\"$param\", ";
        }
        $message = "Parameters $params are required";
        $code = 3002;
        parent::__construct($message, $code);
    }
}
