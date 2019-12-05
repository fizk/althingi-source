<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;
use Althingi\Hydrator;
use Althingi\Model;

class CommitteeDocument extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new Hydrator\CommitteeDocument())
            ->setObject(new Model\CommitteeDocument());

        $this->add([
            'name' => 'document_committee_id',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'document_id',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'issue_id',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'category',
            'type' => 'Zend\Form\Element\Text',
        ]);
        $this->add([
            'name' => 'committee_id',
            'type' => 'Zend\Form\Element\Number',
        ]);
        $this->add([
            'name' => 'part',
            'type' => 'Zend\Form\Element\Text',
        ]);
        $this->add([
            'name' => 'name',
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
            'document_committee_id' => [
                'required' => false,
                'allow_empty' => true,
            ],
            'document_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'issue_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'category' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'committee_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'part' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'name' => [
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
