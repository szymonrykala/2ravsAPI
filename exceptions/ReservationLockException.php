<?php

class ReservationLockException extends Exception
{
    public $httpCode = 423;
    public function __construct(string $message)
    {
        $code = 2008;
        parent::__construct("ReservationLockException: ".$message, $code);
    }
}
