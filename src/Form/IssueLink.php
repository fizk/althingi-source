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

class IssueLink extends Form
{
    public function getModel(): Model\IssueLink
    {
        return (new Hydrator\IssueLink())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\IssueLink()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('from_assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('from_issue_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('from_category'))
                ->attachValidator(new NotEmpty())
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
            (new Input('type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
