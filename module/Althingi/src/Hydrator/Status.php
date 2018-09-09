<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class Status implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Status $object
     * @return \Althingi\Model\Status
     */
    public function hydrate(array $data, $object)
    {
        return $object->setAssemblyId($data['assembly_id'])
            ->setIssueId($data['issue_id'])
            ->setCommitteeId($data['committee_id'])
            ->setSpeechId($data['speech_id'])
            ->setDocumentId($data['document_id'])
            ->setDate($data['date'] ? new DateTime($data['date']) : null)
            ->setTitle($data['title'])
            ->setType($data['type'])
            ->setCommitteeName($data['committee_name'])
            ->setCompleted($data['completed']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Status $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
