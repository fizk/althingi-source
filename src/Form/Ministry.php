<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\ToNull;
use Laminas\Validator\Digits;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class Ministry extends Form
{
    public function getModel(): Model\Ministry
    {
        return (new Hydrator\Ministry())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Ministry()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('ministry_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
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
            (new Input('first', true))
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
                ->attachValidator(new Digits())
            ,
            (new Input('last', true))
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
                ->attachValidator(new Digits())
            ,
        ];
    }
}
