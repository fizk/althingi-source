<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class ConstituencyDate implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\ConstituencyDate $object
     * @return \Althingi\Model\ConstituencyDate
     */
    public function hydrate(array $data, object $object): object
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
     *
     * @param \Althingi\Model\ConstituencyDate $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
