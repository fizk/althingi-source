<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Laminas\Filter\ToNull;
use Althingi\Filter\ToInt;
use Laminas\Validator\{Digits, Date};

class CommitteeMeeting extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\CommitteeMeeting())
            ->setObject(new Model\CommitteeMeeting());
        ;
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'committee_meeting_id' => [
                'name' => 'committee_meeting_id',
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
            'committee_id' => [
                'name' => 'committee_id',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => Digits::class]
                ],
                'filters' => [
                    ['name' => ToInt::class,],
                ],
            ],
            'from' => [
                'name' => 'from',
                'required' => false,
                'allow_empty' => true,
                'validators' => [
                    [
                        'name' => Date::class,
                        'options' => ['step' => 'any', 'format' => 'Y-m-d H:i:s']
                    ]
                ],
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'to' => [
                'name' => 'to',
                'required' => false,
                'allow_empty' => true,
                'validators' => [
                    [
                        'name' => Date::class,
                        'options' => ['step' => 'any', 'format' => 'Y-m-d H:i:s']
                    ]
                ],
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
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
