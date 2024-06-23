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

class President extends Form
{
    public function getModel(): Model\President
    {
        return (new Hydrator\President())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\President()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('president_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('congressman_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('name', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('title', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('abbr', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('from'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d']))
            ,
            (new Input('to', true))
                ->attachFilter(new ToNull(['type' => 'all']))
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d']))
            ,
        ];
    }
}
