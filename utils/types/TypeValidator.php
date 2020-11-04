<?php

namespace utils\types;

use UnexpectedValueException;

class TypeValidator
{
    private string $field;
    protected $value;
    public $type;

    public function __construct(string $field, $value)
    {
        $this->value = $value;
        $this->field = $field;
        $this->validateType();
    }

    public function validateType(): void
    {
        if (gettype($this->value) !== $this->type) {
            throw new UnexpectedValueException('Value of `' . $this->field . '` have to be a type of ' . $this->type, 400);
        }
    }

    public function applyRules(array $schemaParams): void
    {
        /**
         * check and apply rooles defined by the user
         * rules to apply are:
         * > pattern - regex
         * > validate - FILTER_VALIDATE_*
         * > sanitize - FILTER_SANITIZE_*
         * 
         * @param array $schemaParams
         * @return void
         */
        //regex
        if (isset($schemaParams['pattern'])) {
            if (
                !filter_var($this->value, FILTER_VALIDATE_REGEXP, [
                    'options' => [
                        'regexp' => $schemaParams['pattern']
                    ]
                ])
            ) throw new UnexpectedValueException('Value `' . $this->field . '` do not match the pattern: ' . $schemaParams['pattern'], 400);
        }

        //aplying validation
        if (isset($schemaParams['validate'])) {
            if (
                !filter_var($this->value, $schemaParams['validate'])
            ) throw new UnexpectedValueException('Value `' . $this->field . '` do not pass applied validation:' . $schemaParams['validate'], 400);
        }

        // applying filter_validate
        if (isset($schemaParams['sanitize'])) {
            $this->value = filter_var($this->value, $schemaParams['filter']);
        }
    }
}
