<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class IssueCategoryAndTime implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\IssueCategoryAndTime $object
     * @return \Althingi\Model\IssueCategoryAndTime
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setCategoryId($data['category_id'])
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title'])
            ->setTime($data['time']);
    }

    /**
     *
     * @param \Althingi\Model\IssueCategoryAndTime $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
