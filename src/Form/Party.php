<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

class Party extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Party())
            ->setObject(new \Althingi\Model\Party());

        $this->add([
            'name' => 'party_id',
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

        $this->add([
            'name' => 'color',
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
            'party_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'name' => [
                'required' => false,
                'allow_empty' => true,
                // This is because party_id: 26 is used if an MP does not belong to a party,
                // the data coming from althingi.xml has no name for this party 26
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\Callback',
                        'options' => ['callback' => function ($value) {
                            return (empty($value))
                                ? '-'
                                : $value;
                        }]
                    ]
                ],
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
            'color' => [
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
