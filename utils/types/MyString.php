<?php

namespace utils\types;

use UnexpectedValueException;

class MyString
{
    public string $value;
    function __construct($string = '')
    {
        $this->value = filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    function __invoke(string $pattern = ''): string
    {
        if (
            $pattern == '' ||
            !filter_var($this->value, FILTER_VALIDATE_REGEXP, [
                'options' => [
                    'regexp' => $this->$pattern
                ]
            ])
        ) throw new UnexpectedValueException('Value do not match the pattern: ' . $pattern);
        return $this->value;
    }
}
