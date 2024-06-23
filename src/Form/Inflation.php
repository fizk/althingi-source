<?php

namespace Althingi\Form;

use Althingi\Filter\ToFloat;
use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Validator\{Date, NotEmpty, Regex};
use Library\Form\Form;
use Library\Input\Input;

class Inflation extends Form
{
    public function getModel(): Model\Inflation
    {
        return (new Hydrator\Inflation())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Inflation()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('date'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d']))
            ,
            (new Input('value'))
                ->attachFilter(new ToFloat())
                ->attachValidator(new Regex(['pattern' => '/^[0-9]*(\.[0-9]*)?$/']))
            ,
        ];
    }
}
