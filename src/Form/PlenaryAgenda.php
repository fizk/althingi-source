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

class PlenaryAgenda extends Form
{
    public function getModel(): Model\PlenaryAgenda
    {
        return (new Hydrator\PlenaryAgenda())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\PlenaryAgenda()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('item_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('plenary_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('issue_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('issue_name', true))
                ->attachFilter(new ToNull(['type' => 'all']))
                ,
            (new Input('issue_type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('issue_typename', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('assembly_id'))
                ->attachValidator(new NotEmpty())
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
            ,
            (new Input('category'))
                ->attachValidator(new NotEmpty())
            ,
            (new Input('iteration_type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('iteration_continue', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('iteration_comment', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('comment', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('comment_type', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('posed_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('posed', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('answerer_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('answerer', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('counter_answerer_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('counter_answerer', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('instigator_id', true))
                ->attachValidator(new SignedDigits())
                ->attachFilter(new ToInt())
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
            (new Input('instigator', true))
                ->attachFilter(new ToNull(['type' => 'all']))
            ,
        ];
    }
}
