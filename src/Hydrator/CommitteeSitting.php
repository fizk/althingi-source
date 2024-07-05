<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class CommitteeSitting implements HydratorInterface
{
    use HydrateDate;

    /**
     *
     * @param array $data
     * @param \Althingi\Model\CommitteeSitting $object
     * @return \Althingi\Model\CommitteeSitting
     */
    public function hydrate(array $data, object $object): object
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
     *
     * @param \Althingi\Model\CommitteeSitting $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
