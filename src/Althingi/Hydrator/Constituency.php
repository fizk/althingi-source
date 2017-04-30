<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class Constituency implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\Constituency $object
     * @return \Althingi\Model\Constituency
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setConstituencyId($data['constituency_id'])
            ->setName($data['name'])
            ->setAbbrShort($data['abbr_short'])
            ->setAbbrLong($data['abbr_long'])
            ->setDescription($data['description']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\Constituency $object
     * @return array
     */
    public function extract($object)
    {
        return $object->toArray();
    }
}
