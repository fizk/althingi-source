<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

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
    public function extract($object)
    {
        return $object->toArray();
    }
}
