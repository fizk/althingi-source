<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\ToNull;
use Laminas\Validator\{Date, NotEmpty};
use Library\Form\Form;
use Library\Input\Input;

class Congressman extends Form
{
    public function getModel(): Model\Congressman
    {
        return (new Hydrator\Congressman())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Congressman()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('name'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('congressman_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('birth'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d']))
            ,
            (new Input('abbreviation', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('death', true))
                ->attachFilter(new ToNull(['type' => 'all']))
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d']))
            ,
        ];
    }
}
