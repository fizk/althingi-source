<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class PartyAndTime implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\PartyAndTime $object
     * @return \Althingi\Model\PartyAndTime
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setPartyId($data['party_id'])
            ->setName($data['name'])
            ->setAbbrShort($data['abbr_short'])
            ->setAbbrLong($data['abbr_long'])
            ->setColor($data['color'])
            ->setTotalTime($data['total_time']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\PartyAndTime $object
     * @return array
     */
    public function extract($object)
    {
        return (array)$object;
    }
}
