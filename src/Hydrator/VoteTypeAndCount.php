<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class VoteTypeAndCount implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\VoteTypeAndCount $object
     * @return \Althingi\Model\VoteTypeAndCount
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCount($data['count'])
            ->setVote($data['vote']);
    }

    /**
     *
     * @param \Althingi\Model\VoteTypeAndCount $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
