<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class SuperCategory extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\SuperCategory())
            ->setObject(new \Althingi\Model\SuperCategory());

        $this->add([
            'name' => 'super_category_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'title',
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
            'super_category_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'title' => [
                'required' => true,
                'allow_empty' => false,
            ],
        ];
    }
}
