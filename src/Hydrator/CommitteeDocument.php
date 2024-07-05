<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Althingi\Utils\HydratorInterface;

class CommitteeDocument implements HydratorInterface
{
    use HydrateDate;

    /**
     *
     * @param array $data
     * @param \Althingi\Model\CommitteeDocument $object
     * @return \Althingi\Model\CommitteeDocument
     */
    public function hydrate(array $data, object $object): object
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
     *
     * @param \Althingi\Model\CommitteeDocument $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
