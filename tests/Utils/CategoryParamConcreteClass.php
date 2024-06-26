<?php

namespace Althingi\Utils;

use Althingi\Utils\CategoryParam;

class CategoryParamConcreteClass
{
    use CategoryParam;

    private $param;

    public function __construct($param)
    {
        $this->param = $param;
    }

    public function params()
    {
        return $this;
    }

    public function fromQuery($name, $default)
    {
        return $this->param;
    }
}
