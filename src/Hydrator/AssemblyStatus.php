<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class AssemblyStatus implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\AssemblyStatus $object
     * @return \Althingi\Model\AssemblyStatus
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCount($data['count'])
            ->setType($data['type'])
            ->setCategory(array_key_exists('category', $data) ? $data['category'] : null)
            ->setTypeName($data['type_name'])
            ->setTypeSubname($data['type_subname'])
            ->setStatus(array_key_exists('status', $data) ? $data['status'] : null);
    }

    /**
     *
     * @param \Althingi\Model\AssemblyStatus $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
