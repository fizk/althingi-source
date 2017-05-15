<?php

namespace Althingi\Service;

use Althingi\DatabaseConnection;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\Model\Plenary as PlenaryModel;

class PlenaryTest extends PHPUnit_Extensions_Database_TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->pdo);

        $expectedData = (new PlenaryModel())
            ->setPlenaryId(1)
            ->setFrom(new \DateTime('2000-01-01 00:00:00'))
            ->setAssemblyId(1);

        $actualData = $plenaryService->get(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetNotFound()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->pdo);

        $expectedData = null;

        $actualData = $plenaryService->get(1, 100);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByAssembly()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->pdo);

        $expectedData = [
            (new PlenaryModel())->setPlenaryId(1)->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-01')),
            (new PlenaryModel())->setPlenaryId(2)->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-01')),
            (new PlenaryModel())->setPlenaryId(3)
                ->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01'))
                ->setTo(new \DateTime('2001-01-01')),
            (new PlenaryModel())->setPlenaryId(4)
                ->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01'))
                ->setTo(new \DateTime('2001-01-01'))->setName('p-name'),
        ];

        $actualData = $plenaryService->fetchByAssembly(1, 0, 20);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByAssemblyNotFound()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->pdo);

        $expectedData = [];

        $actualData = $plenaryService->fetchByAssembly(100, 0, 20);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCountByAssembly()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->pdo);

        $expectedData = 4;
        $actualData = $plenaryService->countByAssembly(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCountByAssemblyNotFound()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->pdo);

        $expectedData = 0;
        $actualData = $plenaryService->countByAssembly(100);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $plenary = (new PlenaryModel())
            ->setAssemblyId(1)
            ->setPlenaryId(5);

        $assemblyService = new Plenary();
        $assemblyService->setDriver($this->pdo);
        $assemblyService->create($plenary);

        $expectedTable = $this->createArrayDataSet([
            'Plenary' => [
                ['plenary_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 2, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 3, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => '2001-01-01 00:00:00', 'name' => null],
                ['plenary_id' => 4, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => '2001-01-01 00:00:00', 'name' => 'p-name'],
                ['plenary_id' => 5, 'assembly_id' => 1, 'from' => null, 'to' => null, 'name' => null],
            ],
        ])->getTable('Plenary');
        $queryTable = $this->getConnection()->createQueryTable('Plenary', 'SELECT * FROM Plenary');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testUpdate()
    {
        $plenary = (new PlenaryModel())
            ->setAssemblyId(1)
            ->setPlenaryId(1)
            ->setFrom(new \DateTime('2000-01-01 00:00:00'))
            ->setName('NewName');

        $assemblyService = new Plenary();
        $assemblyService->setDriver($this->pdo);
        $assemblyService->update($plenary);

        $expectedTable = $this->createArrayDataSet([
            'Plenary' => [
                ['plenary_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => 'NewName'],
                ['plenary_id' => 2, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 3, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => '2001-01-01 00:00:00', 'name' => null],
                ['plenary_id' => 4, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => '2001-01-01 00:00:00', 'name' => 'p-name'],
            ],
        ])->getTable('Plenary');
        $queryTable = $this->getConnection()->createQueryTable('Plenary', 'SELECT * FROM Plenary');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null]
            ],
            'Plenary' => [
                ['plenary_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 2, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 3, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => '2001-01-01 00:00:00', 'name' => null],
                ['plenary_id' => 4, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => '2001-01-01 00:00:00', 'name' => 'p-name'],
            ]
        ]);
    }
}
