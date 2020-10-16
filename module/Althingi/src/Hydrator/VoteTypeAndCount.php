<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class VoteTypeAndCount implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\VoteTypeAndCount $object
     * @return \Althingi\Model\VoteTypeAndCount $object
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCount($data['count'])
            ->setVote($data['vote']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\VoteTypeAndCount $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
