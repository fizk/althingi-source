<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Laminas\Filter\ToNull;
use Althingi\Validator\SignedDigits;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class CommitteeMeetingAgenda extends Form
{
    public function getModel(): Model\CommitteeMeetingAgenda
    {
        return (new Hydrator\CommitteeMeetingAgenda())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\CommitteeMeetingAgenda()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('committee_meeting_agenda_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('committee_meeting_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('category', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('issue_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('title', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
