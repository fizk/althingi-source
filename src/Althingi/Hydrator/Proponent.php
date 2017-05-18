<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class Proponent implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Proponent $object
     * @return \Althingi\Model\Proponent
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setMinister($data['minister'])
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath($data['death'] ? new DateTime($data['death']) : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Proponent $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
