<?php
namespace Althingi\Store;

use Althingi\Injector\StoreAwareInterface;
use Althingi\Utils\Transformer;
use Althingi\Model;
use Althingi\Hydrator;
use MongoDB\Database;

class Category implements StoreAwareInterface
{
    /** @var \MongoDB\Database */
    private $database;

    public function fetchByAssembly(int $assemblyId)
    {
        $documents = $this->getStore()->issue->aggregate([
            [
                '$match' => [
                    'issue.assembly_id' => $assemblyId
                ],
            ],
            [
                '$unwind' => '$categories'
            ],
            [
                '$group' => [
                    '_id' => [
                        'category_id' => '$categories.category_id',
                    ],
                    'category' => [ '$first' => '$categories'],
                    'count' => ['$sum' => 1]
                ],
            ],
            [
                '$project' => [
                    'count' => '$count',
                    'category' => '$category',
                ]
            ],
            [
                '$sort' => ['count' => -1]
            ]
        ]);


        return array_map(function ($object) {
            return (new Hydrator\CategoryAndCount())->hydrate(
                array_merge((array) $object->category, ['count' => $object->count]),
                new Model\CategoryAndCount()
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
