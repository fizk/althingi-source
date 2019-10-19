<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class Session implements HydratorInterface
{
    use HydrateDate;

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Session $object
     * @return \Althingi\Model\Session
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setSessionId($data['session_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setConstituencyId($data['constituency_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setPartyId(isset($data['party_id']) ? $data['party_id'] : null)
            ->setFrom(array_key_exists('from', $data) ? $this->hydrateDate($data['from']) : null)
            ->setTo(array_key_exists('to', $data) ? $this->hydrateDate($data['to']) : null)
            ->setType(isset($data['type']) ? $data['type'] : null)
            ->setAbbr(isset($data['abbr']) ? $data['abbr'] : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Session $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
