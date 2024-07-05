<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class Party implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Party $object
     * @return \Althingi\Model\Party
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setPartyId(isset($data['party_id']) ? $data['party_id'] : null)
            ->setName($data['name'])
            ->setAbbrShort($data['abbr_short'])
            ->setAbbrLong($data['abbr_long'])
            ->setColor($data['color']);
    }

    /**
     *
     * @param \Althingi\Model\Party $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
