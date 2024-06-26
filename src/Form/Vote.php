<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Filter\ToNoNullInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\IssueKind;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\ToNull;
use Laminas\Validator\{Date, NotEmpty};
use Library\Form\Form;
use Library\Input\Input;

class Vote extends Form
{
    public function getModel(): Model\Vote
    {
        return (new Hydrator\Vote())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Vote()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('vote_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('document_id', true))
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('assembly_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('issue_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('kind'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new IssueKind())
            ,
            (new Input('date'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i:s']))
            ,
            (new Input('type'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('outcome', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('method', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('yes', true))
                ->attachFilter(new ToNoNullInt())
            ,
            (new Input('no', true))
                ->attachFilter(new ToNoNullInt())
            ,
            (new Input('inaction', true))
                ->attachFilter(new ToNoNullInt())
            ,
            (new Input('committee_to', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
