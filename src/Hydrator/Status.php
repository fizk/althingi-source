<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class Status implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Status $object
     * @return \Althingi\Model\Status
     */
    public function hydrate(array $data, object $object): object
    {
        return $object->setAssemblyId($data['assembly_id'])
            ->setIssueId($data['issue_id'])
            ->setCommitteeId($data['committee_id'])
            ->setSpeechId($data['speech_id'])
            ->setDocumentId($data['document_id'])
            ->setDate($data['date'] ? new DateTime($data['date']) : null)
            ->setTitle($data['title'])
            ->setType($data['type'])
            ->setValue($data['value'])
            ->setCommitteeName($data['committee_name'])
            ->setCompleted($data['completed']);
    }

    /**
     *
     * @param \Althingi\Model\Status $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
