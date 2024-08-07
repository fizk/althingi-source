<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Althingi\Utils\HydratorInterface;

class IssueLink implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\IssueLink $object
     * @return \Althingi\Model\IssueLink
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setIssueId($data['to_issue_id'])
            ->setAssemblyId($data['to_assembly_id'])
            ->setKind(KindEnum::fromString($data['to_kind']))
            ->setFromAssemblyId($data['from_assembly_id'])
            ->setFromIssueId($data['from_issue_id'])
            ->setFromKind(KindEnum::fromString($data['from_kind']))
            ->setType($data['type'])
            ;
    }

    /**
     *
     * @param \Althingi\Model\IssueLink $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
