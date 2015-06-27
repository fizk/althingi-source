<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 27/05/15
 * Time: 7:22 AM
 */

namespace Althingi\Model\Dom;

use Zend\Stdlib\Extractor\ExtractionInterface;
use Althingi\Model\Exception as ModelException;

class Session implements ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @param  \DOMElement $object
     * @return array|null
     * @throws \Althingi\Model\Exception
     */
    public function extract($object)
    {
        if (!$object instanceof \DOMElement) {
            throw new ModelException('Not a valid \DOMElement');
        }

        if (!$object->getElementsByTagName('inn')->item(0)) {
            throw new ModelException('Missing [{inn}] value');
        }

        $id = ($object->getElementsByTagName('þing')->length ==1)
            ? $object->getElementsByTagName('þing')->item(0)->nodeValue
            : null ;

        $abbr = ($object->getElementsByTagName('skammstöfun')->length == 1)
            ? $object->getElementsByTagName('skammstöfun')->item(0)->nodeValue
            : null;

        $type = ($object->getElementsByTagName('tegund')->length == 1)
            ? $object->getElementsByTagName('tegund')->item(0)->nodeValue
            : null ;

        $party = ($object->getElementsByTagName('þingflokkur')->length == 1)
            ? $object->getElementsByTagName('þingflokkur')->item(0)->nodeValue
            : null;

        $partyId = ($object->getElementsByTagName('þingflokkur')->length == 1)
            ? $object->getElementsByTagName('þingflokkur')->item(0)->getAttribute('id')
            : null ;

        $constituency = ($object->getElementsByTagName('kjördæmi')->length == 1)
            ? $object->getElementsByTagName('kjördæmi')->item(0)->nodeValue
            : null ;

        $constituencyId = ($object->getElementsByTagName('kjördæmi')->length == 1)
            ? $object->getElementsByTagName('kjördæmi')->item(0)->getAttribute('id')
            : null ;

        $constituencyNo = ($object->getElementsByTagName('kjördæmanúmer')->length == 1)
            ? $object->getElementsByTagName('kjördæmanúmer')->item(0)->nodeValue
            : null ;

        $seat = ($object->getElementsByTagName('þingsalssæti')->length == 1)
            ? $object->getElementsByTagName('þingsalssæti')->item(0)->nodeValue
            : null ;

        $division =  ($object->getElementsByTagName('deild')->length == 1)
            ? $object->getElementsByTagName('deild')->item(0)->nodeValue
            : null ;

        $from = date('Y-m-d', strtotime($object->getElementsByTagName('inn')->item(0)->nodeValue));

        $to = ($object->getElementsByTagName('út')->item(0))
            ? date('Y-m-d', strtotime($object->getElementsByTagName('út')->item(0)->nodeValue))
            : null;

        return [
            'id' => (int) $id,
            'assembly_id' => (int) $id,
            'abbr' => $abbr,
            'type' => $type,
            'party' => $party,
            'party_id' => (int) $partyId,
            'constituency' => $constituency,
            'constituency_id' => $constituencyId,
            'constituency_no' => $constituencyNo,
            'seat' => $seat,
            'division' => $division,
            'from' => $from,
            'to' => $to,
        ];
    }
}
