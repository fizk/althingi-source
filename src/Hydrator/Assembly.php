<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class Assembly implements HydratorInterface
{
    use HydrateDate;

    /**
     *
     * @param array $data
     * @param \Althingi\Model\Assembly $object
     * @return \Althingi\Model\Assembly
     */
    public function hydrate(array $data, object $object): object
    {
        return $object->setAssemblyId($data['assembly_id'])
            ->setFrom(array_key_exists('from', $data) ? $this->hydrateDate($data['from']) : null)
            ->setTo(array_key_exists('to', $data) ? $this->hydrateDate($data['to']) : null)
            ;
    }

    /**
     *
     * @param \Althingi\Model\Assembly $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
