<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Laminas\Hydrator\HydratorInterface;

class Link implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Link $object
     * @return \Althingi\Model\Link
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setKind(KindEnum::fromString($data['kind']))
            ->setType(array_key_exists('type', $data) ? $data['type'] : 'related')
            ;
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Link $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
