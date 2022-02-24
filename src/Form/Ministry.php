<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Althingi\Validator\SignedDigits;
use Laminas\Filter\ToNull;
use Laminas\Validator\Digits;

class Ministry extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new Model\Ministry())
            ->setHydrator(new Hydrator\Ministry());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'ministry_id' => [
                'name' => 'ministry_id',
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
                'required' => true,
                'allow_empty' => false,
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
            'first' => [
                'name' => 'first',
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
                    ['name' => Digits::class]
                ],
            ],
            'last' => [
                'name' => 'last',
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
                    ['name' => Digits::class]
                ],
            ],
        ];
    }
}
