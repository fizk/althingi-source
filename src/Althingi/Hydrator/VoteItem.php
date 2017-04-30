<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class VoteItem implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\VoteItem $object
     * @return \Althingi\Model\VoteItem
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setVoteId($data['vote_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setVote($data['vote'])
            ->setVoteItemId($data['vote_item_id']);
    }

    /**
     * Extract values from an object
     *
     * @param \Althingi\Model\VoteItem $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
