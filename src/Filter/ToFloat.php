<?php

namespace Althingi\Filter;

use Laminas\Filter\AbstractFilter;

class ToFloat extends AbstractFilter
{
    /**
     * @param  mixed $value
     * @return int|mixed
     */
    public function filter($value)
    {
        if (!is_numeric($value)) {
            return null;
        }

        return floatval($value);
    }
}
