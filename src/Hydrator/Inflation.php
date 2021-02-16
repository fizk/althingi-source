<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use DateTime;

class Inflation implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Inflation $object
     * @return \Althingi\Model\Inflation
     */
    public function hydrate(array $data, $object)
    {
        return $object->setId((int) $data['id'])
            ->setValue((float) $data['value'])
            ->setDate($data['date'] ? new DateTime($data['date']) : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Inflation $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
