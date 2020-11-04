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
    // public $array;
    // private string $name;

    // function __construct(string $name, $array)
    // {
    //     if (
    //         gettype($array) === 'int' || gettype($array) === 'boolean'
    //     ) throw new \models\HttpBadRequestException('Value `' . $name . '` have to be a integer type.');

    //     $this->array = $array;
    //     $this->name = $name;
    // }

    // function __toString(): string
    // {
    // $string = '';
    // foreach ($this->array as $item) {
    //     $string .= ';' . $item;
    // }
    // return $string;
    // }

    // function getValue()
    // {
    //     if (gettype($this->array) === 'string') {
    //         return $this->toArray();
    //     } else return $this->__toString();
    // }

    // public function toArray()
    // {
    // $items = explode(';', $this->array);
    // unset($items[0]);
    // return array_values($items);
    // }

    // function validate():void
    // {

    // }
}
