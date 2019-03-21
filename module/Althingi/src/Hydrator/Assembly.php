<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class Assembly implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Assembly $object
     * @return \Althingi\Model\Assembly
     */
    public function hydrate(array $data, $object)
    {
        return $object->setAssemblyId($data['assembly_id'])
            ->setFrom(array_key_exists('from', $data) ? $this->hydrateDate($data['from']) : null)
            ->setTo(array_key_exists('to', $data) ? $this->hydrateDate($data['to']) : null)
            ;
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Assembly $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }

    private function hydrateDate($date)
    {
        if (is_null($date)) {
            return null;
        }

        if (is_string($date)) {
            return new DateTime($date);
        }

        if ($date instanceof DateTime) {
            return $date;
        }

        if ($date instanceof \MongoDB\BSON\UTCDateTime) {
            return $date->toDateTime();
        }
    }
}
