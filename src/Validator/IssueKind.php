<?php

namespace Althingi\Validator;

use Laminas\Validator\AbstractValidator;

class IssueKind extends AbstractValidator
{
    /**
     * Returns true if and only if $value only contains digit characters
     *
     * @param  string $value
     * @return bool
     */
    public function isValid($value)
    {
        $value = strtolower($value);

        if ($value !== 'a' && $value !== 'b') {
            $this->error('value is not A or B');
            return false;
        }
        return true;
    }
}
