<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use DateTime;

class CongressmanAndRange implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CongressmanAndDateRange $object
     * @return \Althingi\Model\CongressmanAndDateRange
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setAbbreviation(array_key_exists('abbreviation', $data) ? $data['abbreviation'] : null)
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath($data['death'] ? new DateTime($data['death']) : null)
            ->setTime($data['time'])
            ->setBegin($data['begin'] ? new DateTime($data['begin']) : null)
            ->setEnd($data['end'] ? new DateTime($data['end']) : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CongressmanAndDateRange $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
