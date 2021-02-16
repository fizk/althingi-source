<?php

namespace Althingi\Hydrator;

use DateTime;
use Laminas\Hydrator\HydratorInterface;

class ConstituencyValue implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\ConstituencyValue $object
     * @return \Althingi\Model\ConstituencyValue
     */
    public function hydrate(array $data, $object)
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
     * Extract values from an object
     *
     * @param  \Althingi\Model\ConstituencyValue $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
