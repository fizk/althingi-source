<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class CommitteeSession implements HydratorInterface
{
    use HydrateDate;

    /**
     *
     * @param array $data
     * @param \Althingi\Model\CommitteeSession $object
     * @return \Althingi\Model\CommitteeSession
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCommitteeSessionId($data['committee_session_id'])
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
     * @param \Althingi\Model\CommitteeSession $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
