<?php

namespace utils\types;

use utils\types\TypeValidator;

final class MyString extends TypeValidator
{
    public $type = 'string';

    public function __construct(string $name, $value)
    {
        parent::__construct($name, $value);
    }

    public function getValue()
    {
        return (string) $this->value;
    }

    public static function parseType(string $value): string
    {
        return $value;
    }
}
