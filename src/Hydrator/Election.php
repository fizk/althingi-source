<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class Election implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Election $object
     * @return \Althingi\Model\Election
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setElectionId($data['election_id'])
            ->setDate($data['date'] ? new DateTime($data['date']) : null)
            ->setTitle($data['title'])
            ->setDescription($data['description']);
    }

    /**
     *
     * @param \Althingi\Model\Election $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
