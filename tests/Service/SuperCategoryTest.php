<?php

namespace Althingi\Service;

use Althingi\Model\SuperCategory as SuperCategoryModel;
use Althingi\Service\SuperCategory;
use Althingi\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use PDO;

class SuperCategoryTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $service = new SuperCategory();
        $service->setDriver($this->pdo);

        $expectedData = (new SuperCategoryModel())
            ->setSuperCategoryId(1)
            ->setTitle('title1');
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $superCategory = (new SuperCategoryModel())
            ->setSuperCategoryId(10)
            ->setTitle('MyTitle');

        $expectedTable = $this->createArrayDataSet([
            'SuperCategory' => [
                ['super_category_id' => 10, 'title' => 'MyTitle'],
            ],
        ])->getTable('SuperCategory');
        $actualTable = $this->getConnection()
            ->createQueryTable('SuperCategory', 'SELECT * FROM SuperCategory where `super_category_id` = 10');

        $service = new SuperCategory();
        $service->setDriver($this->pdo);
        $service->create($superCategory);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $superCategory = (new SuperCategoryModel())
            ->setSuperCategoryId(10)
            ->setTitle('MyTitle');

        $expectedTable = $this->createArrayDataSet([
            'SuperCategory' => [
                ['super_category_id' => 10, 'title' => 'MyTitle'],
            ],
        ])->getTable('SuperCategory');
        $actualTable = $this->getConnection()
            ->createQueryTable('SuperCategory', 'SELECT * FROM SuperCategory where `super_category_id` = 10');

        $service = new SuperCategory();
        $service->setDriver($this->pdo);
        $service->save($superCategory);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $superCategory = (new SuperCategoryModel())
            ->setSuperCategoryId(1)
            ->setTitle('MyTitle');

        $expectedTable = $this->createArrayDataSet([
            'SuperCategory' => [
                ['super_category_id' => 1, 'title' => 'MyTitle'],
            ],
        ])->getTable('SuperCategory');
        $actualTable = $this->getConnection()
            ->createQueryTable('SuperCategory', 'SELECT * FROM SuperCategory where `super_category_id` = 1');

        $service = new SuperCategory();
        $service->setDriver($this->pdo);
        $service->update($superCategory);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'SuperCategory' => [
                ['super_category_id' => 1, 'title' => 'title1'],
                ['super_category_id' => 2, 'title' => 'title2'],
                ['super_category_id' => 3, 'title' => 'title3'],
            ],
        ]);
    }
}
