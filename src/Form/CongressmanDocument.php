<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

class CongressmanDocument extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\CongressmanDocument())
            ->setObject(new \Althingi\Model\CongressmanDocument());

        $this->add([
            'name' => 'issue_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'assembly_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'document_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'congressman_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'order',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'minister',
            'type' => 'Laminas\Form\Element\Text',
        ]);
        $this->add([
            'name' => 'category',
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
            'issue_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'document_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'congressman_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'category' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'order' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'minister' => [
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
