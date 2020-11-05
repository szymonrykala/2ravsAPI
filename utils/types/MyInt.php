<?php

namespace utils\types;

use utils\types\TypeValidator;

final class MyInt extends TypeValidator
{
    public $type = 'integer';

    public function __construct(string $name, $value)
    {
        parent::__construct($name, $value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public static function parseType(int $value): int
    {
        return $value;
    }
}
