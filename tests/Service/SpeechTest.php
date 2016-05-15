<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */
namespace Althingi\Service;

require './module/Althingi/tests/MyAppDbUnitArrayDataSet.php';

use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\MyAppDbUnitArrayDataSet;

class SpeechTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testFetch1stEntry()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->fetch('1_1', 145, 1);

        $this->assertInternalType('array', $data);
        $this->assertCount(6, $data);
        $this->assertEquals('1_1', $data[0]->speech_id);
    }

    public function testFetch2ndEntry()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->fetch('2_1', 145, 1);

        $this->assertInternalType('array', $data);
        $this->assertCount(6, $data);
        $this->assertEquals('2_1', $data[1]->speech_id);
    }

    public function testFetchLastEntry()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->fetch('6_1', 145, 1);

        $this->assertInternalType('array', $data);
        $this->assertCount(6, $data);
        $this->assertEquals('6_1', $data[5]->speech_id);
    }

    public function testFetchSmallerFrame()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->fetch('5_1', 145, 1, 3);

        $this->assertInternalType('array', $data);
        $this->assertCount(3, $data);
        $this->assertEquals('4_1', $data[0]->speech_id);
        $this->assertEquals('6_1', $data[2]->speech_id);
    }

    public function testPosition()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->fetch('5_1', 145, 1, 3);

        $this->assertInternalType('array', $data);
        $this->assertCount(3, $data);
        $this->assertEquals(3, $data[0]->position);
        $this->assertEquals(4, $data[1]->position);
        $this->assertEquals(5, $data[2]->position);
    }

    public function testFetchNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->fetch('not-defined', 145, 1);

        $this->assertInternalType('array', $data);
        $this->assertCount(0, $data);
    }

    public function testGetFound()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->get('1_1');
        $this->assertInstanceOf('\stdClass', $data);
        $this->assertNull($data->position);
    }

    public function testGetNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->get('undefined');
        $this->assertNull($data);
    }

    public function testFetchByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->fetchByIssue(145, 1);

        $this->assertCount(6, $data);
        $this->assertEquals(0, $data[0]->position);
        $this->assertEquals(5, $data[5]->position);
    }

    public function testFetchByIssueNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $data = $service->fetchByIssue(145, 10000);

        $this->assertCount(0, $data);
    }

    public function testFetchFrequencyByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);
        $data = $service->fetchFrequencyByIssue(145, 1);

        $this->assertEquals(60, $data[0]->count);
        $this->assertEquals(118, $data[1]->count);
        $this->assertEquals(42960, $data[2]->count);
    }

    public function testFetchFrequencyByAssembly()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);
        $time = $service->fetchFrequencyByAssembly(145);

        $this->assertEquals(60, $time[0]->time);
        $this->assertEquals(118, $time[1]->time);
        $this->assertEquals(42960, $time[2]->time);
    }

    public function testCountByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $count = $service->countByIssue(145, 1);
        $this->assertEquals(6, $count);
    }

    public function testCountByIssueNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $count = $service->countByIssue(145, 10000);
        $this->assertEquals(0, $count);
    }

    public function testCreate()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $service->create((object) [
            'speech_id' => '7_1',
            'plenary_id' => 1,
            'assembly_id' => 145,
            'issue_id' => 1,
            'congressman_id' => 1
        ]);

        $this->assertNotNull($service->get('7_1'));
    }

    public function testUpdate()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $service->update((object) [
            'speech_id' => '6_1',
            'congressman_type' => 'hundur'
        ]);

        $this->assertEquals('hundur', $service->get('6_1')->congressman_type);
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
        return new MyAppDbUnitArrayDataSet([
            'Congressman' => [
                require './module/Althingi/tests/data/congressman_1.php'
            ],
            'Assembly' => [
                require './module/Althingi/tests/data/assembly_145.php'
            ],
            'Plenary' => [
                require './module/Althingi/tests/data/plenary_145_1.php'
            ],
            'Issue' => [
                require './module/Althingi/tests/data/issue_145_1.php',
                require './module/Althingi/tests/data/issue_145_2.php',
            ],
            'Speech' => [
                ['speech_id' => '1_1', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 1, 'congressman_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => '2000-01-01 00:01:00'],
                ['speech_id' => '2_1', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 1, 'congressman_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null],
                ['speech_id' => '3_1', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 1, 'congressman_id' => 1, 'from' => '2000-02-01 00:00:00', 'to' => '2000-02-01 00:01:00'],
                ['speech_id' => '4_1', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 1, 'congressman_id' => 1, 'from' => '2000-02-02 00:03:01', 'to' => '2000-02-02 00:03:59'],
                ['speech_id' => '5_1', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 1, 'congressman_id' => 1, 'from' => '2000-03-01 00:05:00', 'to' => '2000-03-01 10:00:00'],
                ['speech_id' => '6_1', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 1, 'congressman_id' => 1, 'from' => '2000-03-01 12:00:00', 'to' => '2000-03-01 14:01:00'],

                ['speech_id' => '1_2', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 2, 'congressman_id' => 1],
                ['speech_id' => '2_2', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 2, 'congressman_id' => 1],
                ['speech_id' => '3_2', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 2, 'congressman_id' => 1],
                ['speech_id' => '4_2', 'plenary_id' => 1, 'assembly_id' => 145, 'issue_id' => 2, 'congressman_id' => 1],
            ]
        ]);

    }
}
