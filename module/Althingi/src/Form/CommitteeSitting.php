<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;
use Althingi\Hydrator;
use Althingi\Model;

class CommitteeSitting extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\CommitteeSitting())
            ->setObject(new Model\CommitteeSitting());

        $this->add([
            'name' => 'committee_sitting_id',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'committee_id',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'order',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'role',
            'type' => 'Zend\Form\Element\Text',
        ]);
        $this->add([
            'name' => 'from',
            'type' => 'Zend\Form\Element\Date',
            'options' => [
                'format' => 'Y-m-d'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);
        $this->add([
            'name' => 'to',
            'type' => 'Zend\Form\Element\Date',
            'options' => [
                'format' => 'Y-m-d'
            ],
            'attributes' => [
                'step' => 'any'
            ],
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
            'committee_sitting_id' => [
                'required' => false,
                'allow_empty' => true,
            ],
            'congressman_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'committee_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'order' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'role' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'from' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'to' => [
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
