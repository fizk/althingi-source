<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class PartyAndElection implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\PartyAndElection $object
     * @return \Althingi\Model\PartyAndElection
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setPartyId($data['party_id'])
            ->setName($data['name'])
            ->setAbbrShort($data['abbr_short'])
            ->setAbbrLong($data['abbr_long'])
            ->setColor($data['color'])
            ->setResults($data['result'])
            ->setSeat($data['seat'])
            ->setElectionId($data['election_id'])
            ->setElectionResultId($data['election_result_id'])
            ->setAssemblyId($data['assembly_id']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\PartyAndElection $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
