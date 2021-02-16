<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

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
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'from',
            'type' => 'Laminas\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i:s'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);

        $this->add([
            'name' => 'to',
            'type' => 'Laminas\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i:s'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);

        $this->add([
            'name' => 'plenary_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'assembly_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'issue_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'category',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'congressman_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'congressman_type',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'iteration',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'type',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'text',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'validated',
            'type' => 'Laminas\Form\Element\Text',
        ]);
    }


    /**
     * Should return an array specification compatible with
     * {@link Laminas\InputFilter\Factory::createInputFilter()}.
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
                        'name' => '\Laminas\Filter\ToNull',
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
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'iteration' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'type' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'text' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'validated' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\Boolean',
                        'options' => ['type' => ['all']]
                    ]
                ],
            ],
        ];
    }
}
