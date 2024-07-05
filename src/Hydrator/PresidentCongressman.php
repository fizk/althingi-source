<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class PresidentCongressman implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\PresidentCongressman $object
     * @return \Althingi\Model\PresidentCongressman
     */
    public function hydrate(array $data, object $object): object
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
     *
     * @param \Althingi\Model\PresidentCongressman $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
