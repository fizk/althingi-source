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

class Committee extends Form
{
    public function getModel(): Model\Committee
    {
        return (new Hydrator\Committee())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Committee()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('committee_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('first_assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('last_assembly_id', true))
                ->attachFilter(new ToNull(['type' => 'all']))
                ->attachFilter(new ToInt())
            ,
            (new Input('name'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('abbr_short', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('abbr_long', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
