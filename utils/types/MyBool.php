<?php

namespace utils\types;

use UnexpectedValueException;

use utils\types\TypeValidator;

final class MyBool extends TypeValidator
{
    public $type = 'boolean';

    public function __construct(string $name, $value)
    {
        parent::__construct($name, $value);
    }

    public function getValue()
    {
        return (int) $this->value;
    }

    public static function parseType(bool $value): bool
    {
        return $value;
    }
}
