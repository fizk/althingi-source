<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use DateTime;

class PresidentCongressman implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\PresidentCongressman $object
     * @return \Althingi\Model\PresidentCongressman
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setPresidentId($data['president_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setFrom($data['from'] ? new DateTime($data['from']) : null)
            ->setTo($data['to'] ? new DateTime($data['to']) : null)
            ->setTitle($data['title'])
            ->setName($data['name'])
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath($data['death'] ? new DateTime($data['death']) : null)
            ->setAbbr($data['abbr']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\PresidentCongressman $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
