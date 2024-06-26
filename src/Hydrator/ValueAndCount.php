<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class ValueAndCount implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\ValueAndCount $object
     * @return \Althingi\Model\ValueAndCount
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setValue($data['value'])
            ->setCount($data['count']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\ValueAndCount $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
