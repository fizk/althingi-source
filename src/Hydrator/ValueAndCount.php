<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class ValueAndCount implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\ValueAndCount $object
     * @return \Althingi\Model\ValueAndCount
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setValue($data['value'])
            ->setCount($data['count']);
    }

    /**
     *
     * @param \Althingi\Model\ValueAndCount $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
