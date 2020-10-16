<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

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
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'first_assembly_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'last_assembly_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'abbr_short',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'abbr_long',
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
                        'name' => '\Laminas\Filter\ToNull',
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
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'abbr_long' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
        ];
    }
}
