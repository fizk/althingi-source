<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

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
    public function extract($object)
    {
        return $object->toArray();
    }
}
