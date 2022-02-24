<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Validator\{Digits, Date};
use Althingi\Validator\SignedDigits;

class Vote extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\Vote())
            ->setObject(new Model\Vote());
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
            'category' => [
                'name' => 'category',
                'required' => true,
                'allow_empty' => false,
            ],
            'document_id' => [
                'name' => 'document_id',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => ToInt::class,],
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'vote_id' => [
                'name' => 'vote_id',
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
                        'options' => ['step' => 'any', 'format' => 'Y-m-d H:i:s']
                    ]
                ],
            ],
            'type' => [
                'name' => 'type',
                'required' => true,
                'allow_empty' => false,
            ],
            'outcome' => [
                'name' => 'outcome',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'method' => [
                'name' => 'method',
                'required' => true,
                'allow_empty' => false,
            ],
            'yes' => [
                'name' => 'yes',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
            ],
            'no' => [
                'name' => 'no',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
            ],
            'inaction' => [
                'name' => 'inaction',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
            ],
            'committee_to' => [
                'name' => 'committee_to',
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
