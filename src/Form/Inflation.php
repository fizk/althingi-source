<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

class Inflation extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setObject(new \Althingi\Model\Inflation())
            ->setHydrator(new \Althingi\Hydrator\Inflation());

        $this->add([
            'name' => 'id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'date',
            'type' => 'Laminas\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);

        $this->add([
            'name' => 'value',
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
            'id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'date' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'value' => [
                'required' => true,
                'allow_empty' => false,
            ],
        ];
    }
}
