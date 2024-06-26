<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Laminas\Hydrator\HydratorInterface;

class CommitteeDocument implements HydratorInterface
{
    use HydrateDate;

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CommitteeDocument $object
     * @return \Althingi\Model\CommitteeDocument
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setDocumentCommitteeId($data['document_committee_id'])
            ->setDocumentId($data['document_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setIssueId($data['issue_id'])
            ->setKind(KindEnum::fromString($data['kind']))
            ->setCommitteeId($data['committee_id'])
            ->setPart($data['part'])
            ->setName($data['name']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CommitteeDocument $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
