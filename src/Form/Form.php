<?php

namespace Althingi\Form;

use Laminas\Form\Form as LaminasForm;

class Form extends LaminasForm
{
    public function isValid()
    {
        $this->data = array_merge(
            $this->getHydrator()->extract($this->getObject()),
            is_array($this->data) ? $this->data : []
        );
        return parent::isValid();
    }
}
