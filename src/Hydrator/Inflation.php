<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class Inflation implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Inflation $object
     * @return \Althingi\Model\Inflation
     */
    public function hydrate(array $data, object $object): object
    {
        return $object->setId((int) $data['id'])
            ->setValue((float) $data['value'])
            ->setDate($data['date'] ? new DateTime($data['date']) : null);
    }

    /**
     *
     * @param \Althingi\Model\Inflation $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
