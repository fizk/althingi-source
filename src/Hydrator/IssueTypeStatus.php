<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class IssueTypeStatus implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\IssueTypeStatus $object
     * @return \Althingi\Model\IssueTypeStatus
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCount($data['count'])
            ->setStatus($data['status']);
    }

    /**
     *
     * @param \Althingi\Model\IssueTypeStatus $object
     * @return array
     */
    public function extract(object $object): array
    {
        //FIXME what is this?
        unset($object->time);

        return $object->toArray();
    }
}
