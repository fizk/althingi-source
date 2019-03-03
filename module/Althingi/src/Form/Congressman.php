<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;
use DateTime;

class Congressman extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Congressman())
            ->setObject(new \Althingi\Model\Congressman());

        $this->add([
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'abbreviation',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'birth',
            'type' => 'Zend\Form\Element\Date',
            'options' => [
                'format' => 'Y-m-d'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);

        $this->add([
            'name' => 'death',
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
            'name' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'congressman_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'birth' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'abbreviation' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'death' => [
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
