<?php

namespace Althingi\Hydrator;

use DateTime;
use Zend\Hydrator\HydratorInterface;

class ConstituencyDate implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\ConstituencyDate $object
     * @return \Althingi\Model\ConstituencyDate
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setConstituencyId($data['constituency_id'])
            ->setName($data['name'])
            ->setAbbrShort($data['abbr_short'])
            ->setAbbrLong($data['abbr_long'])
            ->setDescription($data['description'])
            ->setDate($data['date'] ? new DateTime($data['date']) : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\ConstituencyDate $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
