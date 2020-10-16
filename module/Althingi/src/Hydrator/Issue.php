<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class Issue implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Issue $object
     * @return \Althingi\Model\Issue
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setCategory($data['category'])
            ->setName($data['name'])
            ->setSubName($data['sub_name'])
            ->setType($data['type'])
            ->setTypeName($data['type_name'])
            ->setTypeSubname($data['type_subname'])
            ->setStatus($data['status'])
            ->setQuestion($data['question'])
            ->setGoal($data['goal'])
            ->setMajorChanges($data['major_changes'])
            ->setChangesInLaw($data['changes_in_law'])
            ->setCostsAndRevenues($data['costs_and_revenues'])
            ->setDeliveries($data['deliveries'])
            ->setAdditionalInformation($data['additional_information']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Issue $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
