<?php

namespace Althingi\Filter;

use Laminas\Filter\AbstractFilter;

class ToNoNullInt extends AbstractFilter
{
    /**
     * @param  mixed $value
     * @return int|mixed
     */
    public function filter($value)
    {
        if (!is_numeric($value)) {
            return 0;
        }

        return intval($value);
    }
}
