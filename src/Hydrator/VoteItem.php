<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class VoteItem implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\VoteItem $object
     * @return \Althingi\Model\VoteItem
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setVoteId($data['vote_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setVote($data['vote'])
            ->setVoteItemId($data['vote_item_id']);
    }

    /**
     *
     * @param \Althingi\Model\VoteItem $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
