<?php

namespace Althingi\Form;

use Althingi\Model;
use Althingi\Hydrator;
use Zend\InputFilter\InputFilterProviderInterface;

class Ministry extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new Model\Ministry())
            ->setHydrator(new Hydrator\Ministry());

        $this->add([
            'name' => 'ministry_id',
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
            'name' => 'first',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'last',
            'type' => 'Zend\Form\Element\Number',
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
            'ministry_id' => [
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
            'first' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'last' => [
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
