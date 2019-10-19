<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class Document implements HydratorInterface
{
    use HydrateDate;

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Document $object
     * @return \Althingi\Model\Document
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setDocumentId($data['document_id'])
            ->setIssueId($data['issue_id'])
            ->setCategory($data['category'])
            ->setAssemblyId($data['assembly_id'])
            ->setDate(array_key_exists('date', $data) ? $this->hydrateDate($data['date']) : null)
            ->setUrl($data['url'])
            ->setType($data['type']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Document $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
