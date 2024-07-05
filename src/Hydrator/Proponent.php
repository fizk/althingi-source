<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class Proponent implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Proponent $object
     * @return \Althingi\Model\Proponent
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setMinister($data['minister'])
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath($data['death'] ? new DateTime($data['death']) : null);
    }

    /**
     *
     * @param \Althingi\Model\Proponent $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
