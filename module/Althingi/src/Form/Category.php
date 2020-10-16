<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

class Category extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Category())
            ->setObject(new \Althingi\Model\Category());

        $this->add([
            'name' => 'super_category_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'category_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'title',
            'type' => 'Laminas\Form\Element\Text',
        ]);

        $this->add([
            'name' => 'description',
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
            'super_category_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'category_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'title' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'description' => [
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
