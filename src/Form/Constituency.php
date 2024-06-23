<?php

namespace Althingi\Form;

use Althingi\Filter\{NullReplaceFilter, ToInt};
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\ToNull;
use Laminas\Validator\NotEmpty;
use Library\Form\Form;
use Library\Input\Input;

class Constituency extends Form
{
    public function getModel(): Model\Constituency
    {
        return (new Hydrator\Constituency())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Constituency()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('constituency_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('name', true))
                ->attachFilter(new ToNull(['type' => 'all']))
                ->attachFilter(new NullReplaceFilter(['replace' => '-']))
            ,
            (new Input('abbr_short', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('abbr_long', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('description', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
