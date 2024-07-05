<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class Plenary implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Plenary $object
     * @return \Althingi\Model\Plenary
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setAssemblyId($data['assembly_id'])
            ->setPlenaryId($data['plenary_id'])
            ->setName($data['name'])
            ->setFrom($data['from'] ? new DateTime($data['from']) : null)
            ->setTo($data['to'] ? new DateTime($data['to']) : null);
    }

    /**
     *
     * @param \Althingi\Model\Plenary $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
