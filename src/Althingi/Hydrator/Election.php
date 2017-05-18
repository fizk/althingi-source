<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class Election implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Election $object
     * @return \Althingi\Model\Election
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setElectionId($data['election_id'])
            ->setDate($data['date'] ? new DateTime($data['date']) : null)
            ->setTitle($data['title'])
            ->setDescription($data['description']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Election $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
