<?php

namespace Althingi\Form;

use Althingi\Model;
use Althingi\Hydrator;
use Laminas\Filter\ToNull;
use Althingi\Filter\ToInt;
use Laminas\Validator\Date;
use Althingi\Validator\SignedDigits;

class Cabinet extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new Model\Cabinet())
            ->setHydrator(new Hydrator\Cabinet());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'cabinet_id' => [
                'name' => 'cabinet_id',
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
                'name' => 'from',
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
            'title' => [
                'name' => 'title',
                'required' => true,
                'allow_empty' => false,
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
