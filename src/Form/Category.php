<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\ToNull;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class Category extends Form
{
    public function getModel(): Model\Category
    {
        return (new Hydrator\Category())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Category()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('super_category_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt()),

            (new Input('category_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt()),

            (new Input('title'))
                ->attachValidator(new NotEmpty()),

            (new Input('description', true))
                ->attachFilter(new ToNull(['type' => 'all'])),
        ];
    }
}
