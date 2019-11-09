<?php

namespace AlthingiTest\Store;

use AlthingiTest\StorageConnection;
use Althingi\Store;
use Althingi\Model;
use PHPUnit\Framework\TestCase;
use Zumba\PHPUnit\Extensions\Mongo\DataSet\DataSet;

class CategoryTest extends TestCase
{
    use StorageConnection;

    public function testGet()
    {
        $store = (new Store\Category())->setStore($this->database);
        $categories = $store->fetchByAssembly(1);

        $expected = [
            (new Model\CategoryAndCount())
                ->setCount(2)
                ->setCategoryId(1)
                ->setSuperCategoryId(1)
                ->setTitle('title')
                ->setDescription('description'),
            (new Model\CategoryAndCount())
                ->setCount(1)
                ->setCategoryId(2)
                ->setSuperCategoryId(1)
                ->setTitle('title')
                ->setDescription('description'),
        ];

        $this->assertEquals($expected, $categories);
    }

    public function testGetNotFound()
    {
        $store = (new Store\Category())->setStore($this->database);
        $categories = $store->fetchByAssembly(2);

        $expected = [];

        $this->assertEquals($expected, $categories);
    }

    /**
     * Retrieve a DataSet object.
     *
     * @return \Zumba\PHPUnit\Extensions\Mongo\DataSet\DataSet
     */
    protected function getMongoDataSet()
    {
        $dataSet = new DataSet($this->getMongoConnection());
        $dataSet->setFixture([
            'issue' => [
                [
                    'assembly' => ['assembly_id' => 1],
                    "categories" => [
                        [
                            "category_id" => 1,
                            "super_category_id" => 1,
                            "title" => "title",
                            "description" => "description"
                        ]
                    ],
                ],
                [
                    'assembly' => ['assembly_id' => 1],
                    "categories" => [
                        [
                            "category_id" => 1,
                            "super_category_id" => 1,
                            "title" => "title",
                            "description" => "description"
                        ],
                        [
                            "category_id" => 2,
                            "super_category_id" => 1,
                            "title" => "title",
                            "description" => "description"
                        ],
                    ],
                ]
            ]
        ]);

        return $dataSet;
    }
}
