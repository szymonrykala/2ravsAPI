<?php

namespace utils\types;

use Exception;
use UnexpectedValueException;

class MyString
{
    public string $value;
    private string $name;

    function __construct(string $name, string $string)
    {
        $this->name = $name;
        $this->value = $string;
    }

    function getValue(): string
    {
        return (string)$this->value;
    }

    function validate(array $schemaField = []): void
    {
        //regex
        if (isset($schemaField['pattern'])) {
            if (
                !filter_var($this->value, FILTER_VALIDATE_REGEXP, [
                    'options' => [
                        'regexp' => $schemaField['pattern']
                    ]
                ])
            ) throw new UnexpectedValueException('Value `' . $this->name . '` do not match the pattern: ' . $schemaField['pattern'], 400);
        }

        // applying filter
        if (isset($schemaField['filter'])) {
            $this->value = filter_var($this->value, $schemaField['filter']);
        }

        //aplying validation
        if (isset($schemaField['validate'])) {
            if (
                !filter_var($this->value, $schemaField['validate'])
            ) throw new UnexpectedValueException('Value `' . $this->name . '` do not pass applied validation:' . $schemaField['validate'], 400);
        }
    }
}
