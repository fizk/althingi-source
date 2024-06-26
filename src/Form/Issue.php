<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Althingi\Filter\ItemStatusFilter;
use Althingi\Validator\IssueKind;
use Laminas\Filter\ToNull;
use Althingi\Validator\SignedDigits;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class Issue extends Form
{
    public function getModel(): Model\Issue
    {
        return (new Hydrator\Issue())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Issue()
            );
    }

    public function getValidationConfig(): array
    {
        return [
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
            (new Input('congressman_id', true))
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('name'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('sub_name'))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('type'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('type_name'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('type_subname', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('status', true))
                ->attachFilter(new ItemStatusFilter())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('question', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('goal', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('major_changes', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('changes_in_law', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('costs_and_revenues', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('deliveries'))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('additional_information'))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
