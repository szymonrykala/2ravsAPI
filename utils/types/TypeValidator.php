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
    }

    private function type(): void
    {
        if (gettype($this->value) !== $this->type) {
            throw new UnexpectedValueException('Value of `' . $this->field . '` have to be a type of ' . $this->type, 400);
        }
    }

    private function sanitize($sanitizeFilter): void
    {
        // applying filter_validate
        $this->value = filter_var($this->value, $sanitizeFilter);
    }

    private function pattern($regexPattern): void
    {
        //regex
        if (
            !filter_var($this->value, FILTER_VALIDATE_REGEXP, [
                'options' => [
                    'regexp' => $regexPattern
                ]
            ])
        ) throw new UnexpectedValueException('Value `' . $this->field . '` do not match the pattern: ' . $regexPattern, 400);
    }

    private function validate($validationFilter): void
    {
        //aplying validation
        if (
            !filter_var($this->value, $validationFilter)
        ) throw new UnexpectedValueException('Value `' . $this->field . '` do not pass applied validation:' . $validationFilter, 400);
    }

    public function applyRules(array $schemaParams): void
    {
        /**
         * Applying rules, where each rule have to be implemented as function, or be unsetted
         * 
         * @param array $schemaParams
         * @return void
         */
        unset($schemaParams['update'], $schemaParams['create'], $schemaParams['nullable']);

        // applying rules defined in schema
        foreach ($schemaParams as $rule => $value) $this->$rule($value);

    }
}
