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

class CommitteeSession extends Form
{
    public function getModel(): Model\CommitteeSession
    {
        return (new Hydrator\CommitteeSession())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\CommitteeSession()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('committee_session_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('congressman_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('committee_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('order', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('role', true))
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
