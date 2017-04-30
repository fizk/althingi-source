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
use Althingi\Model\Election as ElectionModel;

class ElectionTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $service = new Election();
        $service->setDriver($this->pdo);

        $expectedData = (new ElectionModel())
            ->setElectionId(1)
            ->setDate(new \DateTime('2000-01-01'));
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetByAssembly()
    {
        $service = new Election();
        $service->setDriver($this->pdo);

        $election = $service->getByAssembly(1);
        $this->assertInstanceOf(ElectionModel::class, $election);
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
                ['assembly_id' => 1, 'from' => '2000-01-01'],
                ['assembly_id' => 2, 'from' => '2000-01-01']
            ],
            'Election' => [
                ['election_id' => 1, 'date' => '2000-01-01', 'title' => null, 'description' => null],
                ['election_id' => 2, 'date' => '2000-01-01', 'title' => null, 'description' => null],
                ['election_id' => 3, 'date' => '2000-01-01', 'title' => null, 'description' => null],
                ['election_id' => 4, 'date' => '2000-01-01', 'title' => null, 'description' => null],
            ],
            'Election_has_Assembly' => [
                ['election_id' => 1, 'assembly_id' => 1],
                ['election_id' => 2, 'assembly_id' => 2],
            ]
        ]);
    }
}
