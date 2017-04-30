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

class Cabinet implements HydratorInterface
{

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Cabinet $object
     * @return \Althingi\Model\Cabinet
     */
    public function hydrate(array $data, $object)
    {
        return $object->setCabinetId($data['cabinet_id'])
            ->setName($data['name'] ? : null)
            ->setTitle($data['title'] ? : null);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Assembly $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
