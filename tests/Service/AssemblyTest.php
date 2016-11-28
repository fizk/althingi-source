<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */
namespace Althingi\Service;

use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\Model\Assembly as AssemblyModel;

class AssemblyTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $expectedData = (new AssemblyModel())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'));

        $actualData = $assemblyService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetNotFound()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $actualData = $assemblyService->get(100);

        $this->assertNull($actualData);
    }

    public function testFetch()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [
            (new AssemblyModel())->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01'))
        ];
        $actualData = $assemblyService->fetchAll();

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchEmpty()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [];
        $actualData = $assemblyService->fetchAll(10, 25);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $assembly = (new AssemblyModel())
            ->setAssemblyId(2)
            ->setFrom(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null]
            ],
        ])->getTable('Assembly');
        $actualTable = $this->getConnection()->createQueryTable('Assembly', 'SELECT * FROM Assembly');

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);
        $assemblyService->create($assembly);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $assembly = (new AssemblyModel())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(new \DateTime('2000-02-01'));

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);
        $assemblyService->update($assembly);

        $expectedTable = $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => '2000-02-01']
            ],
        ])->getTable('Assembly');
        $queryTable = $this->getConnection()->createQueryTable('Assembly', 'SELECT * FROM Assembly');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testDelete()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $assemblyService->delete(1);

        $queryTable = $this->getConnection()->createQueryTable('Assembly', 'SELECT * FROM Assembly');

        $expectedTable = $this->createArrayDataSet([
            'Assembly' => [],
        ])->getTable('Assembly');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testCount()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $this->assertEquals(1, $assemblyService->count());
    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        $this->pdo = new PDO(
            $GLOBALS['DB_DSN'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD'],
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]
        );
        return $this->createDefaultDBConnection($this->pdo);
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
        ]);
    }
}
