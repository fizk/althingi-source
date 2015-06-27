<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class Party implements HydratorInterface
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
        if (isset($data['party_id'])) {
            $object->party_id = (int) $data['party_id'];
        }

        $object->name = isset($data['name'])
            ? $data['name']
            : null ;

        $object->abbr_short = isset($data['abbr_short'])
            ? $data['abbr_short']
            : null ;

        $object->abbr_long = isset($data['abbr_long'])
            ? $data['abbr_long']
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
