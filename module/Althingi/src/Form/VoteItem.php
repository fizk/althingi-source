<?php

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

        $this->add([
            'name' => 'vote_item_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'vote_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'congressman_id',
            'type' => 'Zend\Form\Element\Number',
        ]);

        $this->add([
            'name' => 'vote',
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
