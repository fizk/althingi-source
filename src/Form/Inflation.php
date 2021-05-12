<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Laminas\Validator\{Digits, Date};

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
                    ['name' => Digits::class]
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
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => Digits::class]
                ],
            ],
        ];
    }
}
