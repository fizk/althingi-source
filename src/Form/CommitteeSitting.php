<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Laminas\Filter\ToNull;
use Althingi\Filter\ToInt;
use Laminas\Validator\{Date};
use Althingi\Validator\SignedDigits;

class CommitteeSitting extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\CommitteeSitting())
            ->setObject(new Model\CommitteeSitting());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'committee_sitting_id' => [
                'name' => 'committee_sitting_id',
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
            'committee_id' => [
                'name' => 'committee_id',
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
            'order' => [
                'name' => 'order',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'role' => [
                'name' => 'role',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
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
        ];
    }
}
