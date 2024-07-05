<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class PartyAndTime implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\PartyAndTime $object
     * @return \Althingi\Model\PartyAndTime
     */
    public function hydrate(array $data, object $object): object
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
     *
     * @param \Althingi\Model\PartyAndTime $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
