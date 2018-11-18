<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class PlenaryAgenda extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\PlenaryAgenda())
            ->setObject(new \Althingi\Model\PlenaryAgenda());

        $this->add([
            'name' => 'item_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'plenary_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'issue_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'category',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'iteration_type',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'iteration_continue',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'iteration_comment',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'comment',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'comment_type',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'posed_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'posed',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'answerer_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'answerer',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'counter_answerer_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'counter_answerer',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'instigator_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'instigator',
            'type' => 'Zend\Form\Element\Text',
        ]);
    }


    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'item_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'plenary_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'issue_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'category' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'iteration_type' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'iteration_continue' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'iteration_comment' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'comment' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'comment_type' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'posed_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'posed' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'answerer_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'answerer' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'counter_answerer_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'counter_answerer' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'instigator_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'instigator' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
        ];
    }
}
