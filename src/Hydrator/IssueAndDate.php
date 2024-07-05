<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Althingi\Utils\HydratorInterface;
use DateTime;

class IssueAndDate implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\IssueAndDate $object
     * @return \Althingi\Model\IssueAndDate
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
            ->setDate($data['date'] ? new DateTime($data['date']) : null);
    }

    /**
     *
     * @param \Althingi\Model\IssueAndDate $object
     * @return array
     */
    public function extract(object $object): array
    {
        //TODO what is this?
        unset($object->time);

        return $object->toArray();
    }
}
