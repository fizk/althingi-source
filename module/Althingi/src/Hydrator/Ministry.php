<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class Ministry implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Ministry $object
     * @return \Althingi\Model\Ministry
     */
    public function hydrate(array $data, $object)
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
     * Extract values from an object
     *
     * @param  \Althingi\Model\Ministry $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
