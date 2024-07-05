<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class ParliamentarySession implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\ParliamentarySession $object
     * @return \Althingi\Model\ParliamentarySession
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setAssemblyId($data['assembly_id'])
            ->setParliamentarySessionId($data['parliamentary_session_id'])
            ->setName($data['name'])
            ->setFrom($data['from'] ? new DateTime($data['from']) : null)
            ->setTo($data['to'] ? new DateTime($data['to']) : null);
    }

    /**
     *
     * @param \Althingi\Model\ParliamentarySession $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
