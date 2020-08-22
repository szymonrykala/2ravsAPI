<?php

class ActivationException extends Exception
{
    public $httpCode = 401;
    public function __construct(string $reason)
    {
        $message = "Activation exception: $reason";

        $code = 3007;
        parent::__construct($message, $code);
    }
}
