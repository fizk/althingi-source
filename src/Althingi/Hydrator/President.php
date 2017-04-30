<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;
use DateTime;

class President implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\President $object
     * @return \Althingi\Model\President
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setPresidentId($data['president_id'])
            ->setCongressmanId($data['congressman_id'])
            ->setAssemblyId($data['assembly_id'])
            ->setFrom(new DateTime($data['from']))
            ->setTo($data['to'] ? new DateTime($data['to']) : null)
            ->setTitle($data['title'])
            ->setAbbr($data['abbr']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\President $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
