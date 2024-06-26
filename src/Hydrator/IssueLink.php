<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Laminas\Hydrator\HydratorInterface;

class IssueLink implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\IssueLink $object
     * @return \Althingi\Model\IssueLink
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setKind(KindEnum::fromString($data['kind']))
            ->setFromAssemblyId($data['from_assembly_id'])
            ->setFromIssueId($data['from_issue_id'])
            ->setFromKind(KindEnum::fromString($data['from_category']))
            ->setType($data['type'])
            ;
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\IssueLink $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
