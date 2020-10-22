<?php

namespace utils\types;

class MyArray
{
    public array $array;

    function __construct(array $array = [])
    {
        $this->array = $array;
        return $array;
    }

    function __toString(): string
    {
        $string = '';
        foreach ($this->array as $item) {
            $string .= ';' . $item;
        }
        return $string;
    }

    static function toArray(string $separatedString)
    {
        $items = explode(';', $separatedString);
        unset($items[0]);
        return $items;
    }

    function __invoke(): string
    {
        // return $this->value;
        return $this->__toString();
    }
}
