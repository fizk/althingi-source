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

        $this->add(array(
            'name' => 'president_id',
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
            'name' => 'from',
            'type' => 'Zend\Form\Element\Date',
        ));

        $this->add(array(
            'name' => 'to',
            'type' => 'Zend\Form\Element\Date',
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'abbr',
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
