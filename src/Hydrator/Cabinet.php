<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;
use DateTime;

class Cabinet implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Cabinet $object
     * @return \Althingi\Model\Cabinet
     */
    public function hydrate(array $data, object $object): object
    {
        return $object->setCabinetId($data['cabinet_id'])
            ->setTitle($data['title'] ? : null)
            ->setDescription(isset($data['description']) ? $data['description'] : null)
            ->setFrom($data['from'] ? new DateTime($data['from']) : null)
            ->setTo($data['to'] ? new DateTime($data['to']) : null)
            ;
    }

    /**
     *
     * @param \Althingi\Model\Cabinet $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
