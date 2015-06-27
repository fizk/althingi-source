<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class Congressman implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        if (isset($data['congressman_id'])) {
            $object->congressman_id = (int)$data['congressman_id'];
        }
        $object->birth = new \DateTime($data['birth']);
        $object->name = $data['name'];

        return $object;
    }


    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        return (array)$object;
    }
}
