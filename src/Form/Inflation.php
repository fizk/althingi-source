<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Althingi\Filter\ToFloat;
use Laminas\Validator\{Date, Digits, Regex};
use Althingi\Validator\SignedDigits;

class Inflation extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new Model\Inflation())
            ->setHydrator(new Hydrator\Inflation());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
            'date' => [
                'name' => 'date',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    [
                        'name' => Date::class,
                        'options' => ['step' => 'any', 'format' => 'Y-m-d']
                    ]
                ],
            ],
            'value' => [
                'name' => 'value',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToFloat::class,],
                ],
                'validators' => [
                    [
                        'name' => Regex::class,
                        'options' => ['pattern' => '/^[0-9]*(\.[0-9]*)?$/']
                    ]
                ],
            ],
        ];
    }
}
