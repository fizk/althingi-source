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

class CongressmanDocument extends Form
{
    public function getModel(): Model\CongressmanDocument
    {
        return (new Hydrator\CongressmanDocument())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\CongressmanDocument()
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
            (new Input('congressman_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('category'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('order'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('minister', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
