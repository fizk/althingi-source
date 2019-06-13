<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Utils\Transformer;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

class Vote implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    public function fetchFrequencyByAssembly(int $assemblyId)
    {
        $documents = $this->getStore()->vote->aggregate([
            [
                '$match' => [
                    'vote.assembly_id' => $assemblyId
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        'month' => ['$month' => '$vote.date'],
                        'day' => ['$dayOfMonth' => '$vote.date'],
                        'year' => ['$year' => '$vote.date' ],
                    ],
                    'count' => ['$sum' => 1]
                ],
            ],
            [
                '$project' => [
                    'count' => '$count',
                    'date' => [
                        '$dateFromParts' => [
                            'year' => '$_id.year',
                            'month' => '$_id.month',
                            'day' => '$_id.day',
                            'hour' => 0,
                            'minute' => 0,
                            'second' => 0,
                            'timezone' => '+00:00',
                        ],
                    ],
                ]
            ],
            [
                '$sort' => ['date' => 1]
            ]
        ]);

        return array_map(function ($vote) {
            return (new Hydrator\DateAndCount())->hydrate(
                array_merge((array) $vote, ['date' => $vote->date ? $vote->date->toDateTime() : null]),
                new Model\DateAndCount()
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
