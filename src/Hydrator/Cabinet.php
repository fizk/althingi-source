<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use DateTime;

class Cabinet implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Cabinet $object
     * @return \Althingi\Model\Cabinet
     */
    public function hydrate(array $data, $object)
    {
        return $object->setCabinetId($data['cabinet_id'])
            ->setTitle($data['title'] ? : null)
            ->setDescription(isset($data['description']) ? $data['description'] : null)
            ->setFrom($data['from'] ? new DateTime($data['from']) : null)
            ->setTo($data['to'] ? new DateTime($data['to']) : null)
            ;
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
