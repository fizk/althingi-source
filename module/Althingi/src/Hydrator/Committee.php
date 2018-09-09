<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class Committee implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Committee $object
     * @return \Althingi\Model\Committee
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setName($data['name'] ? : null)
            ->setAbbrLong(isset($data['abbr_long']) ? $data['abbr_long'] : null)
            ->setAbbrShort(isset($data['abbr_short']) ? $data['abbr_short'] : null)
            ->setCommitteeId($data['committee_id'])
            ->setFirstAssemblyId($data['first_assembly_id'])
            ->setLastAssemblyId(isset($data['last_assembly_id']) ? $data['last_assembly_id'] : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Committee $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
