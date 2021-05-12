<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class DateAndCount implements HydratorInterface
{
    use HydrateDate;

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\DateAndCount $object
     * @return \Althingi\Model\DateAndCount
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setDate($data['date'] ? $this->hydrateDate($data['date']) : null)
            ->setCount($data['count']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\DateAndCount $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
