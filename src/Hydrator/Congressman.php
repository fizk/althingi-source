<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use DateTime;

class Congressman implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Congressman $object
     * @return \Althingi\Model\Congressman
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath(isset($data['death']) && $data['death'] ? new DateTime($data['death']) : null)
            ->setAbbreviation(array_key_exists('abbreviation', $data) ? $data['abbreviation'] : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Congressman $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
