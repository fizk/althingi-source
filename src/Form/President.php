<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Validator\{Date};
use Althingi\Validator\SignedDigits;

class President extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new Model\President())
            ->setHydrator(new Hydrator\President());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'president_id' => [
                'name' => 'president_id',
                'required' => false,
                'allow_empty' => true,
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
            'title' => [
                'name' => 'title',
                'required' => false,
                'allow_empty' => true,
            ],
            'abbr' => [
                'name' => 'abbr',
                'required' => false,
                'allow_empty' => true,
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
                'validators' => [
                    [
                        'name' => Date::class,
                        'options' => ['step' => 'any', 'format' => 'Y-m-d']
                    ]
                ],
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
