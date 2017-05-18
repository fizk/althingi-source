<?php

namespace Althingi\Form;

use Zend\Form\Form as ZendForm;

class Form extends ZendForm
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
