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

class DateAndCount implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\DateAndCount $object
     * @return \Althingi\Model\DateAndCount
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setDate($data['date'] ? new DateTime($data['date']) : null)
            ->setCount($data['count']);
    }

    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\DateAndCount $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
