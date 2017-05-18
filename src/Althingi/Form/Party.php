<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Party extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Party())
            ->setObject(new \Althingi\Model\Party());

        $this->add(array(
            'name' => 'party_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'abbr_short',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'abbr_long',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'color',
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
            'party_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'name' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'abbr_short' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'abbr_long' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'color' => [
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
