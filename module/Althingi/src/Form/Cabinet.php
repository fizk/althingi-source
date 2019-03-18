<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Cabinet extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new \Althingi\Model\Cabinet())
            ->setHydrator(new \Althingi\Hydrator\Cabinet());

        $this->add([
            'name' => 'cabinet_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'from',
            'type' => 'Zend\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);

        $this->add([
            'name' => 'to',
            'type' => 'Zend\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);

        $this->add([
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'description',
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
            'cabinet_id' => [
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
            'title' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'description' => [
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
