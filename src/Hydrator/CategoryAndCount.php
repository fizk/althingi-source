<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class CategoryAndCount implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\CategoryAndCount $object
     * @return \Althingi\Model\CategoryAndCount
     */
    public function hydrate(array $data, object $object): object
    {
        return $object->setCategoryId($data['category_id'])
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title'] ? : null)
            ->setDescription($data['description'] ? : null)
            ->setCount($data['count']);
    }

    /**
     *
     * @param \Althingi\Model\CategoryAndCount $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
