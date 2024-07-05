<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class VoteItemAndCount implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\VoteItemAndCount $object
     * @return \Althingi\Model\VoteItemAndCount
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setVoteId($data['vote_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setVote($data['vote'])
            ->setVoteItemId($data['vote_item_id'])
            ->setCount($data['count']);
    }

    /**
     *
     * @param \Althingi\Model\VoteItemAndCount $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
