<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class SuperCategory implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\SuperCategory $object
     * @return \Althingi\Model\SuperCategory
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title']);
    }

    /**
     *
     * @param \Althingi\Model\SuperCategory $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
