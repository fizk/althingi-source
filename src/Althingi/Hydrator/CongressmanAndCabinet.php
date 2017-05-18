<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class CongressmanAndCabinet implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CongressmanAndCabinet $object
     * @return \Althingi\Model\CongressmanAndCabinet
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setDate($data['date'] ? new DateTime($data['date']) : null)
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath($data['death'] ? new DateTime($data['death']) : null)
            ->setTitle($data['title']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CongressmanAndCabinet $object
     * @return array
     */
    public function extract($object)
    {
        return (array) $object;
    }
}
