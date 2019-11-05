<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use DateTime;
use MongoDB\Database;
use MongoDB\BSON\UTCDateTime;

class Congressman implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    /**
     * Get congressmen, ordered by speech times.
     *
     * @param int $assemblyId
     * @param int $size
     * @param int $order
     * @return array
     */
    public function fetchTimeByAssembly(int $assemblyId, int $size = 5, int $order = -1): array
    {
        $document = $this->getStore()->congressman->find([
            'assembly.assembly_id' => $assemblyId
        ], ['limit' => $size, 'sort' => ['speech_time' => $order]]);

        return array_map(function ($document) {
            $congressman = array_merge((array)$document['congressman'], ['value' => $document['speech_time']]);
            return  (new Model\CongressmanPartyProperties())
                ->setCongressman(
                    (new Hydrator\CongressmanValue())->hydrate($congressman, new Model\CongressmanValue())
                )->setParty(
                    (new Hydrator\Party())->hydrate((array)$congressman['party'], new Model\Party())
                )->setConstituency(
                    (new Hydrator\Constituency())
                        ->hydrate((array)$congressman['constituency'], new Model\Constituency())
                );
        }, $document->toArray());
    }

    /**
     * Get congressmen order by how often they are on the primary document of a [q, m] Issue.
     * They don't have to be order:1, they just have to be on the document as a proponent.
     *
     * @param int $assemblyId
     * @param int $size
     * @param int $order
     * @return \Althingi\Model\CongressmanPartyProperties[]
     */
    public function fetchQuestionByAssembly(int $assemblyId, int $size = 5, int $order = -1)
    {
        $document = $this->getStore()->congressman->aggregate([
            [
                '$match' => [
                    'assembly.assembly_id' => $assemblyId,
                    '$or' => [
                        ['issues.q' => ['$exists' => 1]],
                        ['issues.m' => ['$exists' => 1]],
                    ]
                ]
            ],
            [
                '$project' => [
                    'value' => ['$sum' => ['$issues.q', '$issues.m']],
                    'congressman' => '$congressman',
                    'issues' => '$issues'
                ]
            ],
            [
                '$sort' => ['value' => -1]
            ],
            [
                '$limit' => $size
            ]
        ]);

        return array_map(function ($document) {
            $congressman = array_merge((array)$document['congressman'], ['value' => $document['value']]);
            return  (new Model\CongressmanPartyProperties())
                ->setCongressman(
                    (new Hydrator\CongressmanValue())->hydrate($congressman, new Model\CongressmanValue())
                )->setParty(
                    (new Hydrator\Party())->hydrate((array)$congressman['party'], new Model\Party())
                )->setConstituency(
                    (new Hydrator\Constituency())
                        ->hydrate((array)$congressman['constituency'], new Model\Constituency())
                );
        }, iterator_to_array($document));
    }

    /**
     * Get congressmen order by how often they are on the primary document of a [q, m] Issue.
     * They don't have to be order:1, they just have to be on the document as a proponent.
     *
     * @param int $assemblyId
     * @param int $size
     * @param int $order
     * @return array
     */
    public function fetchPropositionsByAssembly(int $assemblyId, int $size = 5, int $order = -1)
    {
        $document = $this->getStore()->congressman->aggregate([
            [
                '$match' => [
                    'assembly.assembly_id' => $assemblyId,
                    '$or' => [
                        ['issues.v' => ['$exists' => 1]],
                        ['issues.a' => ['$exists' => 1]],
                        ['issues.f' => ['$exists' => 1]],
                    ]
                ]
            ],
            [
                '$project' => [
                    'value' => ['$sum' => ['$issues.v', '$issues.a', '$issues.f']],
                    'congressman' => '$congressman',
                    'issues' => '$issues'
                ]
            ],
            [
                '$sort' => ['value' => -1]
            ],
            [
                '$limit' => $size
            ]
        ]);

        return array_map(function ($document) {
            $congressman = array_merge((array)$document['congressman'], ['value' => $document['value']]);
            return  (new Model\CongressmanPartyProperties())
                ->setCongressman(
                    (new Hydrator\CongressmanValue())->hydrate($congressman, new Model\CongressmanValue())
                )->setParty(
                    (new Hydrator\Party())->hydrate((array)$congressman['party'], new Model\Party())
                )->setConstituency(
                    (new Hydrator\Constituency())
                        ->hydrate((array)$congressman['constituency'], new Model\Constituency())
                );
        }, iterator_to_array($document));
    }

    public function fetchOtherDocumentsByAssembly(int $assemblyId, int $congressmanId)
    {
        $document = $this->getStore()->congressman->aggregate([
            [
                '$match' => [
                    'assembly.assembly_id' => $assemblyId,
                    'congressman.congressman_id' => $congressmanId
                ]
            ], [
                '$project' => [
                    'motions' => ['$objectToArray' => '$motions']
                ]
            ], [
                '$project' => [
                    'motions' => [
                        '$map' => [
                            'input' => '$motions',
                            'as' => 'item',
                            'in' => [
                                'value' => '$$item.k',
                                'count' => '$$item.v',
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $values = $document->toArray();
        if (count($values) == 0) {
            return [];
        }

        return array_map(function ($item) {
            return (new Hydrator\ValueAndCount())->hydrate((array)$item, new Model\ValueAndCount());
        }, (array)$values[0]['motions']);
    }

    /**
     * Get congressmen order by how often they are on the primary document of a [q, m] Issue.
     * They don't have to be order:1, they just have to be on the document as a proponent.
     *
     * @param int $assemblyId
     * @param int $size
     * @param int $order
     * @return array
     */
    public function fetchBillsByAssembly(int $assemblyId, int $size = 5, int $order = -1)
    {
        $document = $this->getStore()->congressman->aggregate([
            [
                '$match' => [
                    'assembly.assembly_id' => $assemblyId,
                    '$or' => [
                        ['issues.l' => ['$exists' => 1]],
                    ]
                ]
            ],
            [
                '$project' => [
                    'value' => ['$sum' => ['$issues.l']],
                    'congressman' => '$congressman',
                    'issues' => '$issues'
                ]
            ],
            [
                '$sort' => ['value' => -1]
            ],
            [
                '$limit' => $size
            ]
        ]);

        return array_map(function ($document) {
            $congressman = array_merge((array)$document['congressman'], ['value' => $document['value']]);
            return  (new Model\CongressmanPartyProperties())
                ->setCongressman(
                    (new Hydrator\CongressmanValue())->hydrate($congressman, new Model\CongressmanValue())
                )->setParty(
                    (new Hydrator\Party())->hydrate((array)$congressman['party'], new Model\Party())
                )->setConstituency(
                    (new Hydrator\Constituency())
                        ->hydrate((array)$congressman['constituency'], new Model\Constituency())
                );
        }, iterator_to_array($document));
    }

    /**
     * @param int $assemblyId
     * @param null|string $type
     * @return \Althingi\Model\CongressmanPartyProperties[]
     */
    public function fetchByAssembly(int $assemblyId, ?string $type = null)
    {
        $query = array_merge(
            ['assembly.assembly_id' => $assemblyId,],
            $type ? ['sessions' => ['$elemMatch' => ['type' => $type]]] : []
        );

        $document = $this->getStore()->congressman->find(
            $query,
            ['sort' => ['congressman.name' => 1]]
        );

        return array_map(function ($document) use ($assemblyId) {
            $congressman = array_merge((array)$document['congressman']);
            $parties = array_merge((array)$document['parties']);
            return  (new Model\CongressmanPartyProperties())
                ->setCongressman(
                    (new Hydrator\Congressman())->hydrate($congressman, new Model\Congressman())
                )->setParty(
                    (new Hydrator\Party())->hydrate((array)$congressman['party'], new Model\Party())
                )->setParties(
                    array_map(function ($party) {
                        return (new Hydrator\Party())->hydrate((array)$party, new Model\Party());
                    }, (array)$parties)
                )->setConstituency(
                    (new Hydrator\Constituency())
                        ->hydrate((array)$congressman['constituency'], new Model\Constituency())
                )->setAssembly((new Model\Assembly())->setAssemblyId($assemblyId));
        }, $document->toArray());
    }

    /**
     * Get a single Congressman by assembly
     *
     * @param int $assemblyId
     * @param int $congressmanId
     * @return Model\CongressmanPartyProperties
     */
    public function getByAssembly(int $assemblyId, int $congressmanId)
    {
        $document = $this->getStore()->congressman->findOne([
            'assembly.assembly_id' => $assemblyId,
            'congressman.congressman_id' => $congressmanId,
        ]);

        $congressman = (array)$document['congressman'];
        $parties = (array)$document['parties'];

        return  (new Model\CongressmanPartyProperties())
            ->setCongressman(
                (new Hydrator\Congressman())->hydrate($congressman, new Model\Congressman())
            )->setParty(
                (new Hydrator\Party())->hydrate((array)$congressman['party'], new Model\Party())
            )->setParties(
                array_map(function ($party) {
                    return (new Hydrator\Party())->hydrate((array)$party, new Model\Party());
                }, (array)$parties)
            )->setConstituency(
                (new Hydrator\Constituency())
                    ->hydrate((array)$congressman['constituency'], new Model\Constituency())
            )->setAssembly(
                (new Model\Assembly())->setAssemblyId($assemblyId)
            );
    }

    /**
     * Gets average age of all congressmen for a give assembly, regardless of their type
     *
     * @todo test and validate
     * @param int $assemblyId
     * @param DateTime $date
     * @return mixed
     */
    public function getAverageAgeByAssembly(int $assemblyId, DateTime $date)
    {
        /** @var $document \MongoDB\Driver\Cursor */
        $document = $this->getStore()->congressman->aggregate([
            [
                '$match' => [
                    'assembly.assembly_id' => $assemblyId,
                ]
            ],
            [
                '$project' => [
                    'date' => ['$dateFromString' => ['dateString' => '$congressman.birth']],
                ]
            ],
            [
                '$project' => [
                    'time' => [
                        '$subtract' => [new UTCDateTime(strtotime($date->format('c')) * 1000), '$date']
                    ]
                ]
            ],
            [
                '$group' => [
                    '_id' => '',
                    'average' => ['$avg' => '$time']
                ]
            ],
            [
                '$project' => [
                    'time' => [
                        '$divide' => ['$average', 31536000000]
                    ]
                ]
            ]
        ]);

        return array_reduce($document->toArray(), function ($carry, $item) {
            return $carry + $item['time'];
        }, 0);
    }

    public function setStore(Database $database)
    {
        $this->database = $database;
        return $this;
    }

    public function getStore(): Database
    {
        return $this->database;
    }
}
