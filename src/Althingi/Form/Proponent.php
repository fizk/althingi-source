<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:30 PM
 */

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Proponent extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Proponent())
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
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ));
        $this->add(array(
            'name' => 'order',
            'type' => 'Zend\Form\Element\Number',
        ));
        $this->add(array(
            'name' => 'minister',
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
            'congressman_id' => [
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
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
        ];
    }
}
