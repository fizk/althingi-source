<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class Vote implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Vote $object
     * @return \Althingi\Model\Vote $object
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setVoteId($data['vote_id'])
            ->setIssueId($data['issue_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setDocumentId($data['document_id'])
            ->setDate($data['date'] ? new \DateTime($data['date']) : null)
            ->setType($data['type'])
            ->setOutcome($data['outcome'])
            ->setMethod($data['method'])
            ->setYes($data['yes'])
            ->setNo($data['no'])
            ->setInaction($data['inaction'])
            ->setCommitteeTo($data['committee_to']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Vote $object
     * @return array
     */
    public function extract($object)
    {
        return (array) $object;
    }
}
