<?php

namespace Althingi\Filter;

use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;

class NullReplaceFilter implements FilterInterface
{
    private $string = '-';

    public function __construct($options = [])
    {
        if (array_key_exists('replace', $options)) {
            $this->string = $options['replace'];
        }
    }

    /**
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        if (empty($value) || $value === null || $value === false) {
            return $this->string;
        }

        return $value;
    }
}
