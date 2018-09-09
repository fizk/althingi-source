<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

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
            ->setCategory($data['category'])
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\IssueCategory $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
