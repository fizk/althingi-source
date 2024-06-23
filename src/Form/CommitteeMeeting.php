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

class CommitteeMeeting extends Form
{
    public function getModel(): Model\CommitteeMeeting
    {
        return (new Hydrator\CommitteeMeeting())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\CommitteeMeeting()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('committee_meeting_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('committee_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('from'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i:s']))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('to', true))
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i:s']))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('description', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
