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

class Plenary extends Form
{
    public function getModel(): Model\Plenary
    {
        return (new Hydrator\Plenary())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Plenary()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('plenary_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('name', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('from', true))
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i']))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('to', true))
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i']))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
