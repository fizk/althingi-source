<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class IssueCategory extends Form
{
    public function getModel(): Model\IssueCategory
    {
        return (new Hydrator\IssueCategory())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\IssueCategory()
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
            (new Input('category_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('category'))
                ->attachValidator(new NotEmpty())
            ,
        ];
    }
}
