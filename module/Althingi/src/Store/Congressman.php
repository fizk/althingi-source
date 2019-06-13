<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

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
     * @return array
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
