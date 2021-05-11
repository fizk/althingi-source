<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\{NullReplaceFilter, ToInt};
use Laminas\Filter\ToNull;
use Laminas\Validator\Digits;

class Constituency extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\Constituency())
            ->setObject(new Model\Constituency());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'constituency_id' => [
                'name' => 'constituency_id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => Digits::class]
                ],
            ],
            'name' => [
                'name' => 'name',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ], [
                        'name' => NullReplaceFilter::class,
                        'options' => ['replace' => '-']
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
            'description' => [
                'name' => 'description',
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
