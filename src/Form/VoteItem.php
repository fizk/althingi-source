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

class VoteItem extends Form
{
    public function getModel(): Model\VoteItem
    {
        return (new Hydrator\VoteItem())
            ->hydrate(
                $this->getInputChain()->getValues(),
                new Model\VoteItem()
            );
    }

    public function getValidationConfig(): array
    {
        return [
            (new Input('vote_item_id', true))
                ->attachFilter(new ToNull(['type' => 'all']))
                ->attachValidator(new SignedDigits())
            ,
            (new Input('vote_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('congressman_id'))
                ->attachFilter(new ToInt())
                ->attachValidator(new SignedDigits())
                ->attachValidator(new NotEmpty())
            ,
            (new Input('vote'))
                ->attachValidator(new NotEmpty())
            ,
        ];
    }
}
