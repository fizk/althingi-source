<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class Constituency implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Constituency $object
     * @return \Althingi\Model\Constituency
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setConstituencyId($data['constituency_id'])
            ->setName($data['name'])
            ->setAbbrShort($data['abbr_short'])
            ->setAbbrLong($data['abbr_long'])
            ->setDescription($data['description']);
    }

    /**
     *
     * @param \Althingi\Model\Constituency $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
