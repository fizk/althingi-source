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

class CongressmanAndRange implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\CongressmanAndDateRange $object
     * @return \Althingi\Model\CongressmanAndDateRange
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCongressmanId($data['congressman_id'])
            ->setName($data['name'])
            ->setBirth($data['birth'] ? new DateTime($data['birth']) : null)
            ->setDeath($data['death'] ? new DateTime($data['death']) : null)
            ->setTime($data['time'])
            ->setBegin($data['begin'] ? new DateTime($data['begin']) : null)
            ->setEnd($data['end'] ? new DateTime($data['end']) : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\CongressmanAndDateRange $object
     * @return array
     */
    public function extract($object)
    {
        return (array) $object;
    }
}
