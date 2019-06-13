<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class DateAndCount implements HydratorInterface
{

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
            ->setDate($data['date'] ? $this->formatDate($data['date']) : null)
            ->setCount($data['count']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\DateAndCount $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }

    private function formatDate($date): ?DateTime
    {
        if (is_string($date)) {
            return new DateTime($date);
        } elseif ($date instanceof DateTime) {
            return $date;
        } else {
            return null;
        }
    }
}
