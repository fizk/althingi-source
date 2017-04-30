<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:30 PM
 */

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Issue extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Issue())
            ->setObject(new \Althingi\Model\Issue());

        $this->add(array(
            'name' => 'issue_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ));
        $this->add(array(
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
        ));
        $this->add(array(
            'name' => 'sub_name',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'category',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'type_name',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'type_subname',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'question',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'goal',
            'type' => 'Zend\Form\Element\Text',
        ));
        $this->add(array(
            'name' => 'major_changes',
            'type' => 'Zend\Form\Element\Text',
        ));
        $this->add(array(
            'name' => 'changes_in_law',
            'type' => 'Zend\Form\Element\Text',
        ));
        $this->add(array(
            'name' => 'costs_and_revenues',
            'type' => 'Zend\Form\Element\Text',
        ));
        $this->add(array(
            'name' => 'deliveries',
            'type' => 'Zend\Form\Element\Text',
        ));
        $this->add(array(
            'name' => 'additional_information',
            'type' => 'Zend\Form\Element\Text',
        ));
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
            'issue_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'congressman_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'name' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'sub_name' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'category' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'type' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'type_name' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'type_subname' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'status' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ], [
                        'name' => '\Althingi\Filter\ItemStatusFilter'
                    ]
                ],
            ],
            'question' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],

            'goal' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'major_changes' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'changes_in_law' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'costs_and_revenues' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'deliveries' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'additional_information' => [
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
