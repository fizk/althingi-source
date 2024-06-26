<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Laminas\Hydrator\HydratorInterface;

class IssueCategory implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\IssueCategory $object
     * @return \Althingi\Model\IssueCategory
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCategoryId($data['category_id'])
            ->setKind(KindEnum::fromString($data['kind']))
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\IssueCategory $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
