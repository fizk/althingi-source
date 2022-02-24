<?php

namespace Althingi\Form;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Validator\Date;
use Althingi\Validator\SignedDigits;

class Committee extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new Model\Committee())
            ->setHydrator(new Hydrator\Committee());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'committee_id' => [
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,]
                ],
            ],
            'first_assembly_id' => [
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,]
                ],
            ],
            'last_assembly_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => ToInt::class,],
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'name' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'abbr_short' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'abbr_long' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
        ];
    }
}
