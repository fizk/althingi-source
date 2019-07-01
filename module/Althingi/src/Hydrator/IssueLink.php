<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

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
            ->setCategory($data['category'])
            ->setFromAssemblyId($data['from_assembly_id'])
            ->setFromIssueId($data['from_issue_id'])
            ->setFromCategory($data['from_category'])
            ->setType($data['type'])
            ;
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\IssueLink $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
