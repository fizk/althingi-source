<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Althingi\Utils\HydratorInterface;

class Link implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Link $object
     * @return \Althingi\Model\Link
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setKind(KindEnum::fromString($data['kind']))
            ->setType(array_key_exists('type', $data) ? $data['type'] : 'related')
            ;
    }

    /**
     *
     * @param \Althingi\Model\Link $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
