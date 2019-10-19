<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class IssueSuperCategoryAndTime implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\IssueSuperCategoryAndTime $object
     * @return \Althingi\Model\IssueSuperCategoryAndTime
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title'])
            ->setTime($data['time']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\IssueSuperCategoryAndTime $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
