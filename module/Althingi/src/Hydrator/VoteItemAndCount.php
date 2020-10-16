<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class VoteItemAndCount implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\VoteItemAndCount $object
     * @return \Althingi\Model\VoteItemAndCount
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setVoteId($data['vote_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setVote($data['vote'])
            ->setVoteItemId($data['vote_item_id'])
            ->setCount($data['count']);
    }

    /**
     * Extract values from an object
     *
     * @param \Althingi\Model\VoteItem $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
