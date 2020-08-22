<?php

class NothingFoundException extends Exception
{
    public $httpCode = 204;
    public function __construct(string $source)
    {
        $this->source = $source;
        $message = "Nothing found in $source with given parameters";
        $code = 2001;
        parent::__construct($message,$code);
    }
}
