<?php

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class IssueLink extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\IssueLink())
            ->setObject(new \Althingi\Model\IssueLink());

        $this->add([
            'name' => 'from_assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'from_issue_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'from_category',
            'type' => 'Zend\Form\Element\Text',
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
            'name' => 'type',
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
            'from_assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'from_issue_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'from_category' => [
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
            'type' => [
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
