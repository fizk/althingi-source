<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:30 PM
 */

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Plenary extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Plenary())
            ->setObject(new \Althingi\Model\Plenary());

        $this->add(array(
            'name' => 'plenary_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'from',
            'type' => 'Zend\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ));

        $this->add(array(
            'name' => 'to',
            'type' => 'Zend\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i'
            ],
            'attributes' => [
                'step' => 'any'
            ],
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
