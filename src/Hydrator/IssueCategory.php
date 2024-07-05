<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Althingi\Utils\HydratorInterface;

class IssueCategory implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\IssueCategory $object
     * @return \Althingi\Model\IssueCategory
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCategoryId($data['category_id'])
            ->setKind(KindEnum::fromString($data['kind']))
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id']);
    }

    /**
     *
     * @param \Althingi\Model\IssueCategory $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
