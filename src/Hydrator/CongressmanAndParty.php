<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use DateTime;

class CongressmanAndParty implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CongressmanAndParty $object
     * @return \Althingi\Model\CongressmanAndParty
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setAbbreviation(array_key_exists('abbreviation', $data) ? $data['abbreviation'] : null)
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath($data['death'] ? new DateTime($data['death']) : null)
            ->setPartyId($data['party_id']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CongressmanAndParty $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
