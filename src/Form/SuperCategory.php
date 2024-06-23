<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class SuperCategory extends Form
{
    public function getModel(): Model\SuperCategory
    {
        return (new Hydrator\SuperCategory())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\SuperCategory()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('super_category_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
            ,
            (new Input('title'))
                ->attachValidator(new NotEmpty())
            ,
        ];
    }
}
