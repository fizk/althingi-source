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

class Assembly extends Form
{
    public function getValidationConfig(): array
    {
        return [
            (new Input('assembly_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits()),
            (new Input('from'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new Date()),
            (new Input('to', true))
                ->attachValidator(new Date())
                ->attachFilter(new ToNull(['type' => 'all']))
        ];
    }

    public function getModel(): Model\Assembly
    {
        return (new Hydrator\Assembly())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Assembly()
            );
    }
}
