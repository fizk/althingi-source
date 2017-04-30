<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class CommitteeMeeting implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CommitteeMeeting $object
     * @return \Althingi\Model\CommitteeMeeting
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCommitteeMeetingId($data['committee_meeting_id'])
            ->setCommitteeId($data['committee_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setFrom(isset($data['from']) && $data['from'] ? new \DateTime($data['from']) : null)
            ->setTo(isset($data['to']) && $data['to'] ? new \DateTime($data['to']) : null)
            ->setDescription(isset($data['description']) ? $data['description'] : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CommitteeMeeting $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
