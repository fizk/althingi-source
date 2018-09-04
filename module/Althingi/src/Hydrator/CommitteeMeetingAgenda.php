<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

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
            ->setCategory(empty($data['category']) ? null : $data['category'])
            ->setIssueId(isset($data['issue_id']) ? $data['issue_id']: null)
            ->setAssemblyId($data['assembly_id'])
            ->setTitle(isset($data['title']) ? $data['title'] : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CommitteeMeetingAgenda $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
