<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:30 PM
 */

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class Category extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\Category())
            ->setObject(new \Althingi\Model\Category());

        $this->add(array(
            'name' => 'super_category_id',
            'type' => 'Zend\Form\Element\Number',
        ));
        $this->add(array(
            'name' => 'category_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'title',
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
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
        ];
    }
}
