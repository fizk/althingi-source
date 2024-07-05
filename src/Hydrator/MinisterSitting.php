<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class MinisterSitting implements HydratorInterface
{
    use HydrateDate;

    /**
     *
     * @param array $data
     * @param \Althingi\Model\MinisterSitting $object
     * @return \Althingi\Model\MinisterSitting
     */
    public function hydrate(array $data, object $object): object
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
     *
     * @param \Althingi\Model\MinisterSitting $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
