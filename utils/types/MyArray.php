<?php

namespace utils\types;

use utils\types\TypeValidator;

class MyArray extends TypeValidator
{
    public $type = 'array';

    public function __construct(string $name, $value)
    {
        parent::__construct($name, $value);
    }

    private function translateToString($value)
    {
        if (gettype($value) == 'string') return $value;
        $string = '';
        foreach ($value as $_ => $item) {
            $string .= ';' . $item;
        }
        return $string;
    }

    public function getValue()
    {
        return (string) $this->translateToString($this->value);
    }

    public static function parseType(string $value): array
    {
        $items = explode(';', $value);
        unset($items[0]);
        return array_values($items);
    }
}
