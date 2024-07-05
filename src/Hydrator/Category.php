<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class Category implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\Category $object
     * @return \Althingi\Model\Category
     */
    public function hydrate(array $data, object $object): object
    {
        return $object->setCategoryId($data['category_id'])
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title'] ? : null)
            ->setDescription(isset($data['description']) ? $data['description'] : null);
    }

    /**
     *
     * @param \Althingi\Model\Category $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
