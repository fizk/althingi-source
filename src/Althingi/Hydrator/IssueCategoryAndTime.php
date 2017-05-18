<?php

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class IssueCategoryAndTime implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\IssueCategoryAndTime $object
     * @return \Althingi\Model\IssueCategoryAndTime
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCategoryId($data['category_id'])
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title'])
            ->setTime($data['time']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\IssueCategoryAndTime $object
     * @return array
     */
    public function extract($object)
    {
        return (array)$object;
    }
}
