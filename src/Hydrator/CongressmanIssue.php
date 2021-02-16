<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class CongressmanIssue implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CongressmanIssue $object
     * @return \Althingi\Model\CongressmanIssue $object
     */
    public function hydrate(array $data, $object)
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
     * Extract values from an object
     *
     * @param  \Althingi\Model\CongressmanIssue $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
