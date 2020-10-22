<?php

namespace utils\types;

class MyInt
{
    public int $value;
    function __construct($int = '')
    {
        $this->value = filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    }
    function __invoke():int
    {
        return $this->value;
    }
}
