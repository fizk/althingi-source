<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class Category implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Category $object
     * @return \Althingi\Model\Category
     */
    public function hydrate(array $data, $object)
    {
        return $object->setCategoryId($data['category_id'])
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title'] ? : null)
            ->setDescription(isset($data['description']) ? $data['description'] : null);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Category $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
