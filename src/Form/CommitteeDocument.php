<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\ToNull;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class CommitteeDocument extends Form
{
    public function getModel(): Model\CommitteeDocument
    {
        return (new Hydrator\CommitteeDocument())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\CommitteeDocument()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('document_committee_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('document_id'))
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
            (new Input('category'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('committee_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('part', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('name', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
