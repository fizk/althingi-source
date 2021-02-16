<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class VoteItemAndAssemblyIssue implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\VoteItemAndAssemblyIssue $object
     * @return \Althingi\Model\VoteItemAndAssemblyIssue
     */
    public function hydrate(array $data, $object)
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
     * Extract values from an object
     *
     * @param \Althingi\Model\VoteItemAndAssemblyIssue $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
