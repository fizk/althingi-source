<?php

namespace Althingi\Form;

use Althingi\Model;
use Althingi\Hydrator;
use Laminas\Filter\ToNull;
use Althingi\Filter\ToInt;
use Laminas\Validator\Digits;

class Category extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\Category())
            ->setObject(new Model\Category());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'super_category_id' => [
                'name' => 'super_category_id',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => Digits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,]
                ],
            ],
            'category_id' => [
                'name' => 'category_id',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => Digits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,]
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
