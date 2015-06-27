<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:30 PM
 */

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Constituency extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Constituency())
            ->setObject((object)[]);

        $this->add(array(
            'name' => 'constituency_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'abbr_short',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'abbr_long',
            'type' => 'Zend\Form\Element\Text',
        ));

        $this->add(array(
            'name' => 'description',
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
            'constituency_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'name' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'abbr_short' => [
                'required' => false,
                'allow_empty' => true,
            ],
            'abbr_long' => [
                'required' => false,
                'allow_empty' => true,
            ],
            'description' => [
                'required' => false,
                'allow_empty' => true,
            ],
        ];
    }
}
