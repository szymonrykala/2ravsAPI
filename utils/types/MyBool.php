<?php

namespace utils\types;

class MyBool
{
    public bool $value;
    function __construct($int = '')
    {
        $this->value = (bool) $int;
    }
    function __invoke(): bool
    {
        return $this->value;
    }
}
