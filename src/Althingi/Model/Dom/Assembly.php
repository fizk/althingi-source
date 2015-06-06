<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:22 AM
 */

namespace Althingi\Model\Dom;

use Zend\Stdlib\Extractor\ExtractionInterface;

class Assembly implements ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @param  \DOMElement $object
     * @return array|null
     */
    public function extract($object)
    {
        if (!$object instanceof \DOMElement) {
            return null;
        }

        $no = ($object->hasAttribute('númer'))
            ? $object->getAttribute('númer')
            : null ;


        $start = $object->getElementsByTagName('þingsetning')->item(0);
        $from = ($start)
            ? date('Y-m-d', strtotime($start->nodeValue))
            : null ;

        $end = $object->getElementsByTagName('þinglok')->item(0);
        $to = ($end)
            ? date('Y-m-d', strtotime($end->nodeValue))
            : null ;

        return [
            'no' => $no,
            'from' => $from,
            'to' => $to
        ];
    }
}
