<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Althingi\Utils\HydratorInterface;

class IssueValue implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\IssueValue $object
     * @return \Althingi\Model\IssueValue
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setKind(KindEnum::fromString($data['kind']))
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
            ->setAdditionalInformation($data['additional_information'])
            ->setValue($data['value']);
    }

    /**
     *
     * @param \Althingi\Model\IssueValue $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
