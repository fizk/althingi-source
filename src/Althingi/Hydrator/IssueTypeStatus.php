<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 18/05/15
 * Time: 10:43 PM
 */

namespace Althingi\Hydrator;

use Zend\Hydrator\HydratorInterface;

class IssueTypeStatus implements HydratorInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  \Althingi\Model\IssueTypeStatus $object
     * @return \Althingi\Model\IssueTypeStatus
     */
    public function hydrate(array $data, $object)
    {
        return $object
            ->setCount($data['count'])
            ->setStatus($data['status']);
    }


    /**
     * Extract values from an object
     *
     * @param  \Althingi\Model\IssueTypeStatus $object
     * @return array
     */
    public function extract($object)
    {
        unset($object->time);

        return (array) $object;
    }
}
