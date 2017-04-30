<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class SuperCategory implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\SuperCategory $object
     * @return \Althingi\Model\SuperCategory $object
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setSuperCategoryId($data['super_category_id'])
            ->setTitle($data['title']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\SuperCategory $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
