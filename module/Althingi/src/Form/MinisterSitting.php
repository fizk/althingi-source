<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

class MinisterSitting extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new \Althingi\Model\MinisterSitting())
            ->setHydrator(new \Althingi\Hydrator\MinisterSitting());

        $this->add([
            'name' => 'minister_sitting_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'assembly_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'ministry_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'congressman_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'party_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'from',
            'type' => 'Laminas\Form\Element\Date',
        ]);

        $this->add([
            'name' => 'to',
            'type' => 'Laminas\Form\Element\Date',
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
            'minister_sitting_id' => [
                'required' => false,
                'allow_empty' => true,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'ministry_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'congressman_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'party_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
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
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
        ];
    }
}
