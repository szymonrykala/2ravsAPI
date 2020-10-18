<?php
namespace utils;

class Validator
{
    public $postalCode = '/^\d{2}-\d{3}$/';
    public $date = '/^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$/';
    public $time = '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/';
    public $password = '/(?=.{8,})(?=.*[!@#$%^&*])(?=.*[0-9]{2,})(?=.*[A-Z])/';
    public $clearString = '/[-.,\w\s]{3,}/u';

    function __construct()
    {
    }
    public function validateString(string $string, int $minLength = 1): bool
    {
        if (isset($string[$minLength - 1])) return true;
        return false;
    }

    public function validateClearString(string $string): bool
    {
        if (filter_var($string, FILTER_VALIDATE_REGEXP, [
            'options' => [
                'regexp' => $this->clearString
            ]
        ])) return true;
        return false;
    }

    public function validatePostalCode(string $postalCode): bool
    {
        /**
         * Validate postal-code with regex
         * pattern: /^\d{2}-\d{3}$/
         * @param string $postalCode
         * @return bool
         */
        if (filter_var($postalCode, FILTER_VALIDATE_REGEXP, [
            'options' => [
                'regexp' => $this->postalCode
            ]
        ])) return true;
        return false;
    }

    public function sanitizeString(string $string): string
    {
        return filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    public function validateDate(string $date): bool
    {
        /**
         * Validate date format with regex
         * pattern: '/^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$/'
         * @param string $date
         * @return boolean
         */
        if (filter_var($date, FILTER_VALIDATE_REGEXP, [
            'options' => [
                'regexp' => $this->date
            ]
        ])) return true;
        return false;
    }

    public function validateTime(string $time): bool
    {
        /**
         * Validate time format with regex
         * pattern: '/^[0-2][0-4]:[0-5][0-9](:[0-5][0-9])?$/'
         * @param string $time
         * @return boolean
         */
        if (filter_var($time, FILTER_VALIDATE_REGEXP, [
            'options' => [
                'regexp' => $this->time
            ]
        ])) return true;
        return false;
    }

    public function validatePassword(string $password): bool
    {
        /**
         * Validate password policy with regex
         * pattern: '/(?=.{8,})(?=.*[!@#$%^&*])(?=.*[0-9]{2,})(?=.*[A-Z])/'
         * must contain: 
         *  - at least 8 characters long
         *  - at least 1 special char from !@#$%^&*
         *  - at least 2 numbers
         *  - at least 1 uppercase letter
         * @param string $password
         * @return boolean
         */
        if (filter_var($password, FILTER_VALIDATE_REGEXP, [
            'options' => [
                'regexp' => $this->password
            ]
        ])) return true;

        return false;
    }

    public function validateEmail(string $email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
        return false;
    }
}
