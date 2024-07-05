<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class CommitteeMeeting implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\CommitteeMeeting $object
     * @return \Althingi\Model\CommitteeMeeting
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCommitteeMeetingId($data['committee_meeting_id'])
            ->setCommitteeId($data['committee_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setFrom(isset($data['from']) && $data['from'] ? new \DateTime($data['from']) : null)
            ->setTo(isset($data['to']) && $data['to'] ? new \DateTime($data['to']) : null)
            ->setDescription(isset($data['description']) ? $data['description'] : null);
    }

    /**
     *
     * @param \Althingi\Model\CommitteeMeeting $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
