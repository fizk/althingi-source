<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class ConstituencyValue implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\ConstituencyValue $object
     * @return \Althingi\Model\ConstituencyValue
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setConstituencyId($data['constituency_id'])
            ->setName($data['name'])
            ->setAbbrShort($data['abbr_short'])
            ->setAbbrLong($data['abbr_long'])
            ->setDescription($data['description'])
            ->setValue($data['value']);
    }

    /**
     *
     * @param \Althingi\Model\ConstituencyValue $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
