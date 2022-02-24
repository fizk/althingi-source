<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Validator\SignedDigits;
use Althingi\Filter\ToInt;

class IssueCategory extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\IssueCategory())
            ->setObject(new Model\IssueCategory());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'issue_id' => [
                'name' => 'issue_id',
                'required' => true,
                'allow_empty' => false,
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
            'category_id' => [
                'name' => 'category_id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
            'category' => [
                'name' => 'category',
                'required' => true,
                'allow_empty' => false,
            ],
        ];
    }
}
