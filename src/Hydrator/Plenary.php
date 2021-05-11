<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use DateTime;

class Plenary implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Plenary $object
     * @return \Althingi\Model\Plenary
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setAssemblyId($data['assembly_id'])
            ->setPlenaryId($data['plenary_id'])
            ->setName($data['name'])
            ->setFrom($data['from'] ? new DateTime($data['from']) : null)
            ->setTo($data['to'] ? new DateTime($data['to']) : null);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Plenary $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
