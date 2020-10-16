<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

class Plenary extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Plenary())
            ->setObject(new \Althingi\Model\Plenary());

        $this->add([
            'name' => 'plenary_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'assembly_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'name',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'from',
            'type' => 'Laminas\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);

        $this->add([
            'name' => 'to',
            'type' => 'Laminas\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i'
            ],
            'attributes' => [
                'step' => 'any'
            ],
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
            'plenary_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'name' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'from' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'to' => [
                'required' => true,
                'allow_empty' => false,
            ],
        ];
    }
}
