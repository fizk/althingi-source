<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use DateTime;

class AssemblyStatus implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\AssemblyStatus $object
     * @return \Althingi\Model\AssemblyStatus
     */
    public function hydrate(array $data, $object)
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
     * Extract values from an object
     *
     * @param  \Althingi\Model\Assembly $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
