<?php

namespace utils\types;

class MyInt
{
    public int $value;
    private string $name;

    function __construct(string $name, $int)
    {
        $this->name = $name;
        $this->value = $int;
    }

    function getValue(): int
    {
        return (int)$this->value;
    }

    function validate(): void
    {
        if (
            !filter_var($this->value, FILTER_VALIDATE_INT)
        ) throw new \models\HttpBadRequestException('Value `' . $this->name . '` have to be a integer type.');
    }
}
