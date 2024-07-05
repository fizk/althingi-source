<?php

namespace Althingi\Hydrator;

use Althingi\Utils\HydratorInterface;

class IssueSuperCategoryAndTime implements HydratorInterface
{
    /**
     *
     * @param array $data
     * @param \Althingi\Model\IssueSuperCategoryAndTime $object
     * @return \Althingi\Model\IssueSuperCategoryAndTime
     */
    public function hydrate(array $data, object $object): object
    {
        return $object
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title'])
            ->setTime($data['time']);
    }

    /**
     *
     * @param \Althingi\Model\IssueSuperCategoryAndTime $object
     * @return array
     */
    public function extract(object $object): array
    {
        return $object->toArray();
    }
}
