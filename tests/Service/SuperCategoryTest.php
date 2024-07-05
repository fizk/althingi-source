<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Model;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;

class SuperCategoryTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $service = new SuperCategory();
        $service->setDriver($this->getPDO());

        $expectedData = (new Model\SuperCategory())
            ->setSuperCategoryId(1)
            ->setTitle('title1');
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllGenerator()
    {
        $service = new SuperCategory();
        $service->setDriver($this->getPDO());

        $actualData = [];
        foreach ($service->fetchAllGenerator() as $category) {
            $actualData[] = $category;
        }


        $this->assertCount(3, $actualData);
        $this->assertInstanceOf(Model\SuperCategory::class, $actualData[0]);
    }

    #[Test]
    public function createSuccess()
    {
        $superCategory = (new Model\SuperCategory())
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
        $service->setDriver($this->getPDO());
        $service->create($superCategory);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $superCategory = (new Model\SuperCategory())
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
        $service->setDriver($this->getPDO());
        $service->save($superCategory);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function updateSuccess()
    {
        $superCategory = (new Model\SuperCategory())
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
        $service->setDriver($this->getPDO());
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
