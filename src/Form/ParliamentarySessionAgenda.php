<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\IssueKind;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\ToNull;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class ParliamentarySessionAgenda extends Form
{
    public function getModel(): Model\ParliamentarySessionAgenda
    {
        return (new Hydrator\ParliamentarySessionAgenda())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\ParliamentarySessionAgenda()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('item_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
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
            (new Input('issue_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('kind'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new IssueKind())
            ,
            (new Input('issue_name', true))
                ->attachFilter(new ToNull(['type' => 'all']))
                ,
            (new Input('issue_type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('issue_typename', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('iteration_type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('iteration_continue', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('iteration_comment', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('comment', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('comment_type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('posed_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('posed', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('answerer_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('answerer', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('counter_answerer_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('counter_answerer', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('instigator_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('instigator', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
