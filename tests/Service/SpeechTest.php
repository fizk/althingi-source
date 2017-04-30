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
use Althingi\Model\Speech as SpeechModel;
use Althingi\Model\SpeechAndPosition as SpeechAndPositionModel;
use Althingi\Model\DateAndCount as DateAndCountModel;

class SpeechTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testGetSpeech()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = (new SpeechModel())
            ->setSpeechId('id--00001')
            ->setAssemblyId(1)
            ->setPlenaryId(1)
            ->setIssueId(1)
            ->setCongressmanId(1);

        $actualData = $service->get('id--00001');

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetSpeechNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = null;

        $actualData = $service->get('invalid-id');

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetch()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);
        $expectedData = [
            (new SpeechAndPositionModel())
                ->setSpeechId('id--00003')
                ->setAssemblyId(1)
                ->setPlenaryId(1)
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(2),

            (new SpeechAndPositionModel())
                ->setSpeechId('id--00004')
                ->setAssemblyId(1)
                ->setPlenaryId(1)
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(3),
        ];

        $actualData = $service->fetch('id--00004', 1, 1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);
        $expectedData = [];

        $actualData = $service->fetch('id--invalid', 1, 1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);
        $expectedData = [
            (new SpeechAndPositionModel())
                ->setSpeechId('id--00001')
                ->setAssemblyId(1)
                ->setPlenaryId(1)
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(0),

            (new SpeechAndPositionModel())
                ->setSpeechId('id--00002')
                ->setAssemblyId(1)
                ->setPlenaryId(1)
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(1),
        ];

        $actualData = $service->fetchByIssue(1, 1, 0, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCountByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = 4;
        $actualData = $service->countByIssue(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchFrequencyByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-01-01')),
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-02-01')),
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-03-01')),
        ];
        $actualData = $service->fetchFrequencyByIssue(1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchFrequencyByAssembly()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-01-01')),
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-02-01')),
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-03-01')),
        ];
        $actualData = $service->fetchFrequencyByAssembly(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCountTotalTimeByAssemblyAndCongressman()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = 120;
        $actualData = $service->countTotalTimeByAssemblyAndCongressman(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $speech = (new SpeechModel())
            ->setSpeechId('id--20001')
            ->setPlenaryId(1)
            ->setAssemblyId(3)
            ->setIssueId(1)
            ->setCongressmanId(1);

        $expectedTable = $this->createArrayDataSet([
            'Speech' => [
                [
                    'speech_id' => 'id--20001',
                    'plenary_id' => 1,
                    'assembly_id' => 3,
                    'issue_id' => 1,
                    'congressman_id' => 1,
                    'congressman_type' => null,
                    'from' => null,
                    'to' => null,
                    'text' => null,
                    'type' => null,
                    'iteration' => null,
                ]
            ],
        ])->getTable('Speech');
        $actualTable = $this->getConnection()
            ->createQueryTable('Speech', 'SELECT * FROM Speech where `assembly_id` = 3');

        $speechService = new Speech();
        $speechService->setDriver($this->pdo);
        $speechService->create($speech);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $speech = (new SpeechModel())
            ->setSpeechId('id--00001')
            ->setPlenaryId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setCongressmanId(2);

        $expectedTable = $this->createArrayDataSet([
            'Speech' => [
                [
                    'speech_id' => 'id--00001',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'congressman_id' => 2,
                    'congressman_type' => null,
                    'from' => null,
                    'to' => null,
                    'text' => null,
                    'type' => null,
                    'iteration' => null
                ]
            ],
        ])->getTable('Speech');
        $actualTable = $this->getConnection()
            ->createQueryTable('Speech', 'SELECT * FROM Speech where `speech_id` = "id--00001"');

        $speechService = new Speech();
        $speechService->setDriver($this->pdo);
        $speechService->update($speech);

        $this->assertTablesEqual($expectedTable, $actualTable);
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
              ['assembly_id' => 2, 'from' => '2000-01-01'],
              ['assembly_id' => 3, 'from' => '2000-01-01'],
            ],
            'Issue' => [
                ['assembly_id' => 1, 'issue_id' => 1],
                ['assembly_id' => 1, 'issue_id' => 2],
                ['assembly_id' => 1, 'issue_id' => 3],
                ['assembly_id' => 2, 'issue_id' => 1],
                ['assembly_id' => 2, 'issue_id' => 2],
                ['assembly_id' => 2, 'issue_id' => 3],
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'congressman 1', 'birth' => '2000-01-01'],
                ['congressman_id' => 2, 'name' => 'congressman 1', 'birth' => '2000-01-01'],
                ['congressman_id' => 3, 'name' => 'congressman 1', 'birth' => '2000-01-01'],
            ],
            'Plenary' => [
              ['plenary_id' => 1, 'assembly_id' => 1],
              ['plenary_id' => 2, 'assembly_id' => 1],
              ['plenary_id' => 3, 'assembly_id' => 1],
            ],
            'Speech' => [
                [
                    'speech_id' => 'id--00001',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                ],[
                    'speech_id' => 'id--00002',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                ],[
                    'speech_id' => 'id--00003',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                ],[
                    'speech_id' => 'id--00004',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                ],[
                    'speech_id' => 'id--10001',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                ],[
                    'speech_id' => 'id--10002',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'congressman_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2000-01-01 00:01:00',
                ],[
                    'speech_id' => 'id--10003',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'congressman_id' => 1,
                    'from' => '2000-02-01 00:00:00',
                    'to' => '2000-02-01 00:01:00',
                ],[
                    'speech_id' => 'id--10004',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'congressman_id' => 2,
                    'from' => '2000-03-01 00:00:00',
                    'to' => '2000-03-01 00:01:00',
                ]
            ]
        ]);
    }
}
