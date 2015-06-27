<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class Plenary implements HydratorInterface
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
        return (object) $data;
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
