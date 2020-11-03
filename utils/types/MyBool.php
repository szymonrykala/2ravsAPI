<?php

namespace utils\types;

use UnexpectedValueException;

class MyBool
{
    public bool $value;
    private string $name;
    function __construct(string $name, $bool)
    {
        $this->name = $name;
        $this->value = $bool;
    }

    function getValue(): int
    {
        /**
         * Get value to database
         * 
         * Must to return int, becouse of MySQL database type parsing.
         * When passing false, the value is '' (empty) and type error is returned
         * 
         * @return int
         */ 
        return (int)$this->value;
    }

    function validate(): void
    {
        /**
         * Validating the passed in constructor value
         * 
         * @return void
         */
        if (
            gettype($this->value)!=='boolean'
        ) throw new UnexpectedValueException('Value `' . $this->name . '` have to be a boolean type.',400);
    }
}
