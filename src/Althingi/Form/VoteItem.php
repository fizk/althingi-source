<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:30 PM
 */

namespace Althingi\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class VoteItem extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new \Althingi\Hydrator\VoteItem())
            ->setObject(new \Althingi\Model\VoteItem());

        $this->add(array(
            'name' => 'vote_item_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'vote_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'vote',
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
            'vote_item_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'vote_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'congressman_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'vote' => [
                'required' => true,
                'allow_empty' => false,
            ],
        ];
    }
}
