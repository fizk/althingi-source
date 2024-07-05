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

class ParliamentarySession extends Form
{
    public function getModel(): Model\ParliamentarySession
    {
        return (new Hydrator\ParliamentarySession())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\ParliamentarySession()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('parliamentary_session_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('name', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('from', true))
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i']))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('to', true))
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i']))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
