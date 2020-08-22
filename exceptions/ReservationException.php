<?php

class ReservationException extends Exception
{
    public $httpCode = 400;
    public function __construct(string $message)
    {
        // $message = "Reservation time exception $message";
        $code = 2006;
        parent::__construct($message, $code);
    }
}
