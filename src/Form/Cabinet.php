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

class Cabinet extends Form
{
    public function getModel(): Model\Cabinet
    {
        return (new Hydrator\Cabinet())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Cabinet()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('cabinet_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits()),

            (new Input('from'))
                ->attachValidator(new Date())
                ->attachValidator(new NotEmpty()),

            (new Input('to', true))
                ->attachValidator(new Date())
                ->attachFilter(new ToNull(['type' => 'all'])),

            (new Input('title'))
                ->attachValidator(new NotEmpty()),

            (new Input('description', true))
                ->attachFilter(new ToNull(['type' => 'all'])),
        ];
    }
}
