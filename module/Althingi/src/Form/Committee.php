<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Committee extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new \Althingi\Model\Committee())
            ->setHydrator(new \Althingi\Hydrator\Committee());

        $this->add([
            'name' => 'committee_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'first_assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'last_assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'abbr_short',
            'type' => 'Zend\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'abbr_long',
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
            'committee_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'first_assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'last_assembly_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
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
        ];
    }
}
