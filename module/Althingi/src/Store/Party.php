<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Utils\Transformer;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

class Party implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    public function fetchTimeByAssembly(int $assemblyId)
    {
        $documents = $this->getStore()->speech->aggregate([
            [
                '$match' => [
                    'issue.assembly_id' => $assemblyId
                ],
            ],
            [
                '$group' => [
                    '_id' => '$congressman.party.party_id',
                    'party' => [ '$first' => '$congressman.party'],
                    'total' => [ '$sum' => '$time']
                ],
            ],
            [
                '$project' => [
                    'count' => '$total',
                    'party' => '$party',
                ]
            ],
        ]);

        return array_map(function ($object) {
            return (new Hydrator\PartyAndTime())->hydrate(
                array_merge((array) $object->party, ['total_time' => $object->count]),
                new Model\PartyAndTime()
            );
        }, iterator_to_array($documents));
    }

    /**
     * @param Database $database
     * @return $this
     */
    public function setStore(Database $database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return Database
     */
    public function getStore(): Database
    {
        return $this->database;
    }
}
