<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\{Callback, ToNull};

class Party extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\Party())
            ->setObject(new Model\Party());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'party_id' => [
                'name' => 'party_id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
            'name' => [
                'name' => 'name',
                'required' => false,
                'allow_empty' => true,
                // This is because party_id: 26 is used if an MP does not belong to a party,
                // the data coming from althingi.xml has no name for this party 26
                'filters' => [
                    [
                        'name' => Callback::class,
                        'options' => ['callback' => function ($value) {
                            return (empty($value))
                                ? '-'
                                : $value;
                        }]
                    ]
                ],
            ],
            'abbr_short' => [
                'name' => 'abbr_short',
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
                'name' => 'abbr_long',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'color' => [
                'name' => 'color',
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
