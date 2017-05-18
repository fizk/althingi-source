<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Congressman extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Congressman())
            ->setObject(new \Althingi\Model\Congressman());

        $this->add(array(
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'birth',
            'type' => 'Zend\Form\Element\Date',
        ));

        $this->add(array(
            'name' => 'death',
            'type' => 'Zend\Form\Element\Date',
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
