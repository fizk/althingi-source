<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class CongressmanDocument implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CongressmanDocument $object
     * @return \Althingi\Model\CongressmanDocument
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setDocumentId($data['document_id'])
            ->setIssueId($data['issue_id'])
            ->setCategory($data['category'])
            ->setAssemblyId($data['assembly_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setMinister(isset($data['minister']) ? $data['minister'] : null)
            ->setOrder($data['order']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CongressmanDocument $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
