<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Validator\Digits;

class CommitteeMeetingAgenda extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\CommitteeMeetingAgenda())
            ->setObject(new Model\CommitteeMeetingAgenda());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'committee_meeting_agenda_id' => [
                'name' => 'committee_meeting_agenda_id',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => ToInt::class,],
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
                'validators' => [
                    ['name' => Digits::class]
                ],
            ],
            'committee_meeting_id' => [
                'name' => 'committee_meeting_id',
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
                    ['name' => ToInt::class,]
                ],
            ],
            'category' => [
                'name' => 'category',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'issue_id' => [
                'name' => 'issue_id',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    ['name' => ToInt::class,],
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
                'validators' => [
                    ['name' => Digits::class]
                ],
            ],
            'title' => [
                'name' => 'title',
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
