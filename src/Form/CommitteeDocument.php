<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Laminas\Filter\ToNull;
use Althingi\Filter\ToInt;
use Laminas\Validator\Digits;

class CommitteeDocument extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\CommitteeDocument())
            ->setObject(new Model\CommitteeDocument());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'document_committee_id' => [
                'name' => 'document_committee_id',
                'required' => false,
                'allow_empty' => true,
                'validators' => [
                    ['name' => Digits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,],
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'document_id' => [
                'name' => 'document_id',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => Digits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,]
                ],
            ],
            'assembly_id' => [
                'name' => 'assembly_id',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => Digits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,],
                ],
            ],
            'issue_id' => [
                'name' => 'issue_id',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => Digits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,]
                ],
            ],
            'category' => [
                'name' => 'category',
                'required' => true,
                'allow_empty' => false,
            ],
            'committee_id' => [
                'name' => 'committee_id',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => Digits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,]
                ],
            ],
            'part' => [
                'name' => 'part',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
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
                    ]
                ],
            ],
        ];
    }
}
