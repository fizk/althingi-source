<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Laminas\Hydrator\HydratorInterface;

class CommitteeMeetingAgenda implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CommitteeMeetingAgenda $object
     * @return \Althingi\Model\CommitteeMeetingAgenda
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCommitteeMeetingAgendaId($data['committee_meeting_agenda_id'])
            ->setCommitteeMeetingId($data['committee_meeting_id'])
            ->setKind($data['kind'] ? KindEnum::fromString($data['kind']) : null)
            ->setIssueId(isset($data['issue_id']) ? $data['issue_id'] : null)
            ->setAssemblyId($data['assembly_id'])
            ->setTitle(isset($data['title']) ? $data['title'] : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CommitteeMeetingAgenda $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
