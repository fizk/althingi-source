<?php

namespace Althingi\Validator;

use Althingi\Filter\ToInt;
use Laminas\Validator\AbstractValidator;

class SignedDigits extends AbstractValidator
{
    private const NOT_DIGITS   = 'notDigits';
    private const STRING_EMPTY = 'digitsStringEmpty';
    private const INVALID      = 'digitsInvalid';

    /**
     * Digits filter used for validation
     *
     * @var \Althingi\Filter\ToInt
     */
    protected static $filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_DIGITS   => 'The input must contain only digits',
        self::STRING_EMPTY => 'The input is an empty string',
        self::INVALID      => 'Invalid type given. String, integer or float expected',
    ];

    /**
     * Returns true if and only if $value only contains digit characters
     *
     * @param  string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        if ('' === $this->getValue()) {
            $this->error(self::STRING_EMPTY);
            return false;
        }

        if (null === static::$filter) {
            static::$filter = new ToInt();
        }

        if ($this->getValue() !== static::$filter->filter($this->getValue())) {
            $this->error(self::NOT_DIGITS);
            return false;
        }

        return true;
    }
}
