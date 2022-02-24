<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Laminas\Filter\{Boolean, ToNull};
use Laminas\Validator\{Date};
use Althingi\Validator\SignedDigits;

class Speech extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\Speech())
            ->setObject(new Model\Speech());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'speech_id' => [
                'name' => 'speech_id',
                'required' => true,
                'allow_empty' => false,
            ],
            'to' => [
                'name' => 'to',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    [
                        'name' => Date::class,
                        'options' => ['step' => 'any', 'format' => 'Y-m-d H:i:s']
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
                        'options' => ['step' => 'any', 'format' => 'Y-m-d H:i:s']
                    ]
                ],
            ],
            'plenary_id' => [
                'name' => 'plenary_id',
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
            'issue_id' => [
                'name' => 'issue_id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
            'category' => [
                'name' => 'category',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
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
            'congressman_type' => [
                'name' => 'congressman_type',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'iteration' => [
                'name' => 'iteration',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
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
            'text' => [
                'name' => 'text',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'validated' => [
                'name' => 'validated',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => Boolean::class,
                        'options' => ['type' => ['all']]
                    ]
                ],
            ],
        ];
    }
}
