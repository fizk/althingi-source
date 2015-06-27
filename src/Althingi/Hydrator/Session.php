<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class Session implements HydratorInterface
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
        if (isset($data['session_id'])) {
            $object->session_id = (int) $data['session_id'];
        }

        if (isset($data['congressman_id'])) {
            $object->congressman_id = (int) $data['congressman_id'];
        }

        if (isset($data['constituency_id'])) {
            $object->constituency_id = (int) $data['constituency_id'];
        }

        if (isset($data['assembly_id'])) {
            $object->assembly_id = (int) $data['assembly_id'];
        }

        if (isset($data['party_id'])) {
            $object->party_id = (int) $data['party_id'];
        }

        $object->from = new \DateTime($data['from']);
        $object->to = isset($data['to'])
            ? new \DateTime($data['to'])
            : null ;

        $object->type = isset($data['type'])
            ? $data['type']
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
        if (isset($object->constituency) && $object->constituency != null) {
            $object->constituency_id = $object->constituency->id;
        }
        unset($object->constituency);

        if (isset($object->party) && $object->party != null) {
            $object->party_id = $object->party->id;
        }
        unset($object->party);

        return (array)$object;
    }
}
