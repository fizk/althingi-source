<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Althingi\Utils\HydratorInterface;

class Vote implements HydratorInterface
{
    use HydrateDate;

    /**
     *
     * @param array $data
     * @param \Althingi\Model\Vote $object
     * @return \Althingi\Model\Vote
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setVoteId($data['vote_id'])
            ->setIssueId($data['issue_id'])
            ->setKind(KindEnum::fromString($data['kind']))
            ->setAssemblyId($data['assembly_id'])
            ->setDocumentId(isset($data['document_id']) ? $data['document_id'] : null)
            ->setDate(array_key_exists('date', $data) ? $this->hydrateDate($data['date']) : null)
            ->setType($data['type'])
            ->setOutcome(isset($data['outcome']) ? $data['outcome'] : null)
            ->setMethod($data['method'])
            ->setYes(isset($data['yes']) ? $data['yes'] : null)
            ->setNo(isset($data['no']) ? $data['no'] : null)
            ->setInaction(isset($data['inaction']) ? $data['inaction'] : null)
            ->setCommitteeTo(isset($data['committee_to']) ? $data['committee_to'] : null);
    }

    /**
     *
     * @param \Althingi\Model\Vote $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
