<?php

namespace Althingi\Form;

use Althingi\Model;
use Althingi\Hydrator;
use Laminas\Filter\ToNull;
use Althingi\Filter\ToInt;
use Laminas\Validator\Date;
use Althingi\Validator\SignedDigits;

class Assembly extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new Model\Assembly())
            ->setHydrator(new Hydrator\Assembly());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'assembly_id' => [
                'name' => 'assembly_id',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,]
                ],
            ],
            'from' => [
                'name'  => 'from',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => Date::class]
                ],
            ],
            'to' => [
                'name' => 'to',
                'required' => false,
                'allow_empty' => true,
                'validators' => [
                    ['name' => Date::class]
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
