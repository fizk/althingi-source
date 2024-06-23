<?php

namespace Althingi\Form;

use Althingi\Filter\ToInt;
use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\{Boolean, ToNull};
use Laminas\Validator\{Date, NotEmpty};
use Library\Form\Form;
use Library\Input\Input;

class Speech extends Form
{
    public function getModel(): Model\Speech
    {
        return (new Hydrator\Speech())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\Speech()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('speech_id'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('to'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i:s']))
            ,
            (new Input('from'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new Date(['step' => 'any', 'format' => 'Y-m-d H:i:s']))
            ,
            (new Input('plenary_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
            ,
            (new Input('assembly_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('issue_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('category', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('congressman_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('congressman_type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('iteration', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('text', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('validated', true))
                ->attachFilter(new Boolean(['type' => ['all']]))
            ,
        ];
    }
}
