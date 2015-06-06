<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class Assembly implements HydratorInterface
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
        if (isset($data['assembly_id'])) {
            $object->assembly_id = (int)$data['assembly_id'];
        }
        $object->from = new \DateTime($data['from']);
        $object->to = isset($data['to'])
            ? new \DateTime($data['to'])
            : null ;
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
