<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class CongressmanIssue implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\CongressmanIssue $object
     * @return \Althingi\Model\CongressmanIssue
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCount($data['count'])
            ->setDocumentType($data['document_type'])
            ->setOrder($data['order'])
            ->setType($data['type'])
            ->setTypeName($data['type_name'])
            ->setTypeSubname($data['type_subname']);
    }

    /**
     *
     * @param \Althingi\Model\CongressmanIssue $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
