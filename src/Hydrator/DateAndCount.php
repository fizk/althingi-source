<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class DateAndCount implements HydratorInterface
{
    use HydrateDate;

    /**
     *
     * @param array $data
     * @param \Althingi\Model\DateAndCount $object
     * @return \Althingi\Model\DateAndCount
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setDate($data['date'] ? $this->hydrateDate($data['date']) : null)
            ->setCount($data['count']);
    }

    /**
     *
     * @param \Althingi\Model\DateAndCount $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
