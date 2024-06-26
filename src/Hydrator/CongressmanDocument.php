<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Laminas\Hydrator\HydratorInterface;

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
            ->setKind(KindEnum::fromString($data['kind']))
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
    public function extract($object): array
    {
        return $object->toArray();
    }
}
