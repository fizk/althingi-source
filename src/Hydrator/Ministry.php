<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class Ministry implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Ministry $object
     * @return \Althingi\Model\Ministry
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setMinistryId($data['ministry_id'])
            ->setName($data['name'])
            ->setAbbrShort($data['abbr_short'])
            ->setAbbrLong($data['abbr_long'])
            ->setFirst($data['first'])
            ->setLast($data['last'])
            ;
    }

    /**
     *
     * @param \Althingi\Model\Ministry $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
