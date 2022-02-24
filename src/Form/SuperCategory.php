<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Althingi\Filter\ToInt;

class SuperCategory extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\SuperCategory())
            ->setObject(new Model\SuperCategory());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'super_category_id' => [
                'name' => 'super_category_id',
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
                'required' => true,
                'allow_empty' => false,
            ],
        ];
    }
}
