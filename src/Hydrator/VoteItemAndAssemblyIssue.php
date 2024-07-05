<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class VoteItemAndAssemblyIssue implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\VoteItemAndAssemblyIssue $object
     * @return \Althingi\Model\VoteItemAndAssemblyIssue
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setAssemblyId($data['assembly_id'])
            ->setVoteId($data['vote_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setVote($data['vote'])
            ->setVoteItemId($data['vote_item_id'])
            ->setIssueId($data['issue_id']);
    }

    /**
     *
     * @param \Althingi\Model\VoteItemAndAssemblyIssue $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
