<?php

namespace Althingi\Form;

use Laminas\InputFilter\InputFilterProviderInterface;

class Document extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Document())
            ->setObject(new \Althingi\Model\Document());

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
            'name' => 'committee_id',
            'type' => 'Laminas\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'date',
            'type' => 'Laminas\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ]);
        $this->add([
            'name' => 'url',
            'type' => 'Laminas\Form\Element\Text',
        ]);
        $this->add([
            'name' => 'type',
            'type' => 'Laminas\Form\Element\Text',
        ]);
        $this->add([
            'name' => 'note',
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
            'committee_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'date' => [
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'url' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Laminas\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'type' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'category' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'note' => [
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
