<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class Assembly implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Assembly $object
     * @return \Althingi\Model\Assembly
     */
    public function hydrate(array $data, $object)
    {
        return $object->setAssemblyId($data['assembly_id'])
            ->setFrom(new DateTime($data['from']))
            ->setTo($data['to'] ? new DateTime($data['to']) : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Assembly $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
