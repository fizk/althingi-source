<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class President implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\President $object
     * @return \Althingi\Model\President
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setPresidentId($data['president_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setFrom(new DateTime($data['from']))
            ->setTo($data['to'] ? new DateTime($data['to']) : null)
            ->setTitle($data['title'])
            ->setAbbr($data['abbr']);
    }

    /**
     *
     * @param \Althingi\Model\President $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
