<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Session extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Session())
            ->setObject(new \Althingi\Model\Session());

        $this->add([
            'name' => 'session_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'constituency_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
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

        $this->add([
            'name' => 'type',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'party_id',
            'type' => 'Zend\Form\Element\Number',
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
            'session_id' => [
                'required' => false,
                'allow_empty' => true,
            ],
            'congressman_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'constituency_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
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
            'type' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'party' => [
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
