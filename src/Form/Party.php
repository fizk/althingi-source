<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\{Callback, ToNull};
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class Party extends Form
{
    public function getModel(): Model\Party
    {
        return (new Hydrator\Party())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Party()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('party_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('name', true))
                // This is because party_id: 26 is used if an MP does not belong to a party,
                // the data coming from althingi.xml has no name for this party 26
                ->attachFilter(new Callback(['callback' => function ($value) {
                    return (empty($value))
                        ? '-'
                        : $value;
                }]))
            ,
            (new Input('abbr_short', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('abbr_long', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('color', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
