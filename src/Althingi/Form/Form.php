<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 12/06/15
 * Time: 6:43 AM
 */

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
