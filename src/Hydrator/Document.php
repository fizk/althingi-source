<?php

namespace Althingi\Hydrator;

use Althingi\Model\KindEnum;
use Althingi\Utils\HydratorInterface;

class Document implements HydratorInterface
{
    use HydrateDate;

    /**
     *
     * @param array $data
     * @param \Althingi\Model\Document $object
     * @return \Althingi\Model\Document
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setDocumentId($data['document_id'])
            ->setIssueId($data['issue_id'])
            ->setKind(KindEnum::fromString($data['kind']))
            ->setAssemblyId($data['assembly_id'])
            ->setDate(array_key_exists('date', $data) ? $this->hydrateDate($data['date']) : null)
            ->setUrl($data['url'])
            ->setType($data['type']);
    }

    /**
     *
     * @param \Althingi\Model\Document $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
