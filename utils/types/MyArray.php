<?php

namespace utils\types;

class MyArray
{
    public $array;
    private string $name;

    function __construct(string $name, $array)
    {
        if (
            gettype($array) === 'int' || gettype($array) === 'boolean'
        ) throw new \models\HttpBadRequestException('Value `' . $name . '` have to be a integer type.');

        $this->array = $array;
        $this->name = $name;
    }

    function __toString(): string
    {
        $string = '';
        foreach ($this->array as $item) {
            $string .= ';' . $item;
        }
        return $string;
    }

    function getValue()
    {
        if (gettype($this->array) === 'string') {
            return $this->toArray();
        } else return $this->__toString();
    }

    public function toArray()
    {
        $items = explode(';', $this->array);
        unset($items[0]);
        return array_values($items);
    }

    function validate():void
    {

    }
}
