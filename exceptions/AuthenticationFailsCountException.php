
<?php

class AuthenticationFailsCountException extends Exception
{
    public $httpCode = 403;
    public function __construct(int $failsCount)
    {
        $message = "Account is blocked. Login fails count is $failsCount";
        $code = 3004;
        parent::__construct($message, $code);
    }
}
