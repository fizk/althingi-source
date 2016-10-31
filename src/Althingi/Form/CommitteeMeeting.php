<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:30 PM
 */

namespace Althingi\Form;

use Zend\Hydrator\HydratorInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class CommitteeMeeting extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct(get_class($this));
        $this
            ->setHydrator(new class implements HydratorInterface {
                public function hydrate(array $data, $object)
                {
                    return (object) $data;
                }

                public function extract($object)
                {
                    return (array)$object;
                }
            })
            ->setObject((object)[]);

        $this->add(array(
            'name' => 'committee_meeting_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'assembly_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'committee_id',
            'type' => 'Zend\Form\Element\Number',
        ));

        $this->add(array(
            'name' => 'from',
            'type' => 'Zend\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i:s'
            ],
            'attributes' => [
                'step' => 'any'
            ],
        ));

        $this->add(array(
            'name' => 'to',
            'type' => 'Zend\Form\Element\DateTime',
            'options' => [
                'format' => 'Y-m-d H:i:s'
            ],
            'attributes' => [
                'step' => 'any'
            ],
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
            'committee_meeting_id' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'assembly_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'committee_id' => [
                'required' => true,
                'allow_empty' => false,
            ],
            'from' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
            ],
            'to' => [
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => '\Zend\Filter\ToNull',
                        'options' => ['type' => 'all']
                    ]
                ],
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
