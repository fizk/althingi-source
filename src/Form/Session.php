<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\ToNull;
use Laminas\Validator\Date;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class Session extends Form
{
    public function getModel(): Model\Session
    {
        return (new Hydrator\Session())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Session()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('session_id', true))
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('congressman_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('constituency_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('assembly_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('from'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d']))
            ,
            (new Input('to', true))
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d']))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('party_id', true))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
