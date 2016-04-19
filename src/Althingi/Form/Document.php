<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:30 PM
 */

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Document extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Document())
            ->setObject((object)[]);

        $this->add(array(
            'name' => 'issue_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'document_id',
            'type' => 'Zend\Form\Element\Number',
        ));
        $this->add(array(
            'name' => 'committee_id',
            'type' => 'Zend\Form\Element\Number',
        ));
        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Text',
        ));
        $this->add(array(
            'name' => 'url',
            'type' => 'Zend\Form\Element\Text',
        ));
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Text',
        ));
        $this->add(array(
            'name' => 'note',
            'type' => 'Zend\Form\Element\Text',
        ));
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
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'date' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'url' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'type' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'note' => [
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
