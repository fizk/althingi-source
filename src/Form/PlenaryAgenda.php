<?php

namespace Althingi\Form;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Filter\ToInt;
use Laminas\Filter\ToNull;
use Althingi\Validator\SignedDigits;

class PlenaryAgenda extends Form
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\PlenaryAgenda())
            ->setObject(new Model\PlenaryAgenda());
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'item_id' => [
                'name' => 'item_id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
            'plenary_id' => [
                'name' => 'plenary_id',
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => ToInt::class,],
                ],
                'validators' => [
                    ['name' => SignedDigits::class]
                ],
            ],
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
            'issue_name' => [
                'name' => 'issue_name',
                'required' => false,
            ],
            'issue_type' => [
                'name' => 'issue_type',
                'required' => false,
            ],
            'issue_typename' => [
                'name' => 'issue_typename',
                'required' => false,
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
            'iteration_type' => [
                'name' => 'iteration_type',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'iteration_continue' => [
                'name' => 'iteration_continue',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'iteration_comment' => [
                'name' => 'iteration_comment',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'comment' => [
                'name' => 'comment',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'comment_type' => [
                'name' => 'comment_type',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'posed_id' => [
                'name' => 'posed_id',
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
                    ['name' => SignedDigits::class]
                ],
            ],
            'posed' => [
                'name' => 'posed',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'answerer_id' => [
                'name' => 'answerer_id',
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
                    ['name' => SignedDigits::class]
                ],
            ],
            'answerer' => [
                'name' => 'answerer',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'counter_answerer_id' => [
                'name' => 'counter_answerer_id',
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
                    ['name' => SignedDigits::class]
                ],
            ],
            'counter_answerer' => [
                'name' => 'counter_answerer',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => ToNull::class,
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'instigator_id' => [
                'name' => 'instigator_id',
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
                    ['name' => SignedDigits::class]
                ],
            ],
            'instigator' => [
                'name' => 'instigator',
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
