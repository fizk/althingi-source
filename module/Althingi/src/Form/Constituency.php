<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Constituency extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Constituency())
            ->setObject(new \Althingi\Model\Constituency());

        $this->add([
            'name' => 'constituency_id',
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
            'constituency_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'name' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ], [
                        'name' => '\Althingi\Filter\NullReplaceFilter',
                        'options' => ['replace' => '-']
                    ]
                ],
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
