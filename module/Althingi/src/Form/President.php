<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class President extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new \Althingi\Model\President())
            ->setHydrator(new \Althingi\Hydrator\President());

        $this->add([
            'name' => 'president_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'from',
            'type' => 'Zend\Form\Element\Date',
        ]);

        $this->add([
            'name' => 'to',
            'type' => 'Zend\Form\Element\Date',
        ]);

        $this->add([
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'abbr',
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
            'president_id' => [
                'required' => false,
                'allow_empty' => true,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'congressman_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'title' => [
                'required' => false,
                'allow_empty' => true,
            ],
            'abbr' => [
                'required' => false,
                'allow_empty' => true,
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
