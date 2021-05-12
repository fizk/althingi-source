<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class Party implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Party $object
     * @return \Althingi\Model\Party
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setPartyId(isset($data['party_id']) ? $data['party_id'] : null)
            ->setName($data['name'])
            ->setAbbrShort($data['abbr_short'])
            ->setAbbrLong($data['abbr_long'])
            ->setColor($data['color']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Party $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
