<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class CongressmanAndCabinet implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\CongressmanAndCabinet $object
     * @return \Althingi\Model\CongressmanAndCabinet
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setDate($data['date'] ? new DateTime($data['date']) : null)
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setAbbreviation(array_key_exists('abbreviation', $data) ? $data['abbreviation'] : null)
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath($data['death'] ? new DateTime($data['death']) : null)
            ->setTitle($data['title']);
    }

    /**
     *
     * @param \Althingi\Model\CongressmanAndCabinet $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
