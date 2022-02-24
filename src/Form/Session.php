<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Laminas\Filter\ToNull;
use Althingi\Filter\ToInt;
use Laminas\Validator\Date;
use Althingi\Validator\SignedDigits;

class Session extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\Session())
            ->setObject(new Model\Session());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'session_id' => [
                'name' => 'session_id',
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
            'congressman_id' => [
                'name' => 'congressman_id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
            'constituency_id' => [
                'name' => 'constituency_id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
            'assembly_id' => [
                'name' => 'assembly_id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
            'from' => [
                'name' => 'from',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    [
                        'name' => Date::class,
                        'options' => ['step' => 'any', 'format' => 'Y-m-d']
                    ]
                ],
            ],
            'to' => [
                'name' => 'to',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
                'validators' => [
                    [
                        'name' => Date::class,
                        'options' => ['step' => 'any', 'format' => 'Y-m-d']
                    ]
                ],
            ],
            'type' => [
                'name' => 'type',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'party_id' => [
                'name' => 'party_id',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => ToInt::class,],
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
        ];
    }
}
