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

class Document extends Form
{
    public function getModel(): Model\Document
    {
        return (new Hydrator\Document())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Document()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('issue_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('document_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('committee_id', true))
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('date'))
                ->attachValidator(new NotEmpty())
                ->attachFilter(new ToNull(['type' => 'all']))
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i']))
            ,
            (new Input('url', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('type'))
                ->attachValidator(new NotEmpty())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('category'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('note', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
