<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class CongressmanValue implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CongressmanValue $object
     * @return \Althingi\Model\CongressmanValue
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setAbbreviation(array_key_exists('abbreviation', $data) ? $data['abbreviation'] : null)
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath($data['death'] ? new DateTime($data['death']) : null)
            ->setValue($data['value']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CongressmanValue $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
