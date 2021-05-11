<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class CommitteeSitting implements HydratorInterface
{
    use HydrateDate;

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CommitteeSitting $object
     * @return \Althingi\Model\CommitteeSitting
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCommitteeSittingId($data['committee_sitting_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setCommitteeId($data['committee_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setOrder($data['order'])
            ->setRole($data['role'])
            ->setFrom($this->hydrateDate($data['from']))
            ->setTo($this->hydrateDate($data['to']));
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CommitteeSitting $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
