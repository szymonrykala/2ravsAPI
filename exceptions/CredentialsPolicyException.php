<?php

use Slim\Psr7\Response;

class CredentialsPolicyException extends Exception
{
    public $httpCode = 400;
    public function __construct(string $name)
    {
        $message = "Credentials policy is not preserved - $name";
        $code = 3005;
        parent::__construct($message, $code);
    }
}
