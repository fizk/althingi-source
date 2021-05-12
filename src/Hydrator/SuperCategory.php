<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class SuperCategory implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\SuperCategory $object
     * @return \Althingi\Model\SuperCategory $object
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\SuperCategory $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
