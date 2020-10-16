<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

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
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'assembly_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'congressman_id',
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

        $this->add([
            'name' => 'title',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'abbr',
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
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
        ];
    }
}
