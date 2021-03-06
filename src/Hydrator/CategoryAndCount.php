<?php

namespace Althingi\Hydrator;

use Laminas\Hydrator\HydratorInterface;

class CategoryAndCount implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CategoryAndCount $object
     * @return \Althingi\Model\CategoryAndCount
     */
    public function hydrate(array $data, $object)
    {
        return $object->setCategoryId($data['category_id'])
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title'] ? : null)
            ->setDescription($data['description'] ? : null)
            ->setCount($data['count']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CategoryAndCount $object
     * @return array
     */
    public function extract($object): array
    {
        return $object->toArray();
    }
}
