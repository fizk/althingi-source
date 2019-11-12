<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class MinisterSitting implements HydratorInterface
{
    use HydrateDate;

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\MinisterSitting $object
     * @return \Althingi\Model\MinisterSitting
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setMinisterSittingId($data['minister_sitting_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setMinistryId($data['ministry_id'])
            ->setPartyId($data['party_id'])
            ->setFrom(array_key_exists('from', $data) ? $this->hydrateDate($data['from']) : null)
            ->setTo(array_key_exists('to', $data) ? $this->hydrateDate($data['to']) : null)
            ;
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\MinisterSitting $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
