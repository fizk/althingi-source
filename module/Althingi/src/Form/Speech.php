<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Speech extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Speech())
            ->setObject(new \Althingi\Model\Speech());

        $this->add([
            'name' => 'speech_id',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'from',
            'type' => 'Zend\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i:s'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);

        $this->add([
            'name' => 'to',
            'type' => 'Zend\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i:s'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);

        $this->add([
            'name' => 'plenary_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'issue_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'category',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'congressman_type',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'iteration',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'type',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'text',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'validated',
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
            'speech_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'to' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'from' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'plenary_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'issue_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'category' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'congressman_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'congressman_type' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'iteration' => [
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
            'text' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'validated' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\Boolean',
                        'options' => ['type' => ['all']]
                    ]
                ],
            ],
        ];
    }
}
