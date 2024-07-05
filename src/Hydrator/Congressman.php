<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class Congressman implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Congressman $object
     * @return \Althingi\Model\Congressman
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath(isset($data['death']) && $data['death'] ? new DateTime($data['death']) : null)
            ->setAbbreviation(array_key_exists('abbreviation', $data) ? $data['abbreviation'] : null);
    }

    /**
     *
     * @param \Althingi\Model\Congressman $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
