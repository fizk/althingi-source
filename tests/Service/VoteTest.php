<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */
namespace Althingi\Service;

use Althingi\Model\Vote as VoteModel;
use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\Model\DateAndCount as DateAndCountModel;

class VoteTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testVote()
    {
        $service = new Vote();
        $service->setDriver($this->pdo);

        $expectedData = (new VoteModel())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setDate(new \DateTime('2000-01-01'));
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testVoteNotFound()
    {
        $service = new Vote();
        $service->setDriver($this->pdo);

        $expectedData = null;
        $actualData = $service->get(10000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByIssue()
    {
        $service = new Vote();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new VoteModel())
                ->setVoteId(1)
                ->setIssueId(1)
                ->setAssemblyId(1)
                ->setDocumentId(1)
                ->setDate(new \DateTime('2000-01-01')),
            (new VoteModel())
                ->setVoteId(2)
                ->setIssueId(1)
                ->setAssemblyId(1)
                ->setDocumentId(2)
                ->setDate(new \DateTime('2000-02-01')),
        ];

        $actualData = $service->fetchByIssue(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchDateFrequencyByIssue()
    {
        $service = new Vote();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new DateAndCountModel())->setCount(1)->setDate(new \DateTime('2000-01-01')),
            (new DateAndCountModel())->setCount(1)->setDate(new \DateTime('2000-02-01')),
        ];
        $actualData = $service->fetchDateFrequencyByIssue(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchFrequencyByAssembly()
    {
        $service = new Vote();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new DateAndCountModel())->setCount(5),
            (new DateAndCountModel())->setCount(1)->setDate(new \DateTime('2000-01-01')),
            (new DateAndCountModel())->setCount(1)->setDate(new \DateTime('2000-02-01')),
        ];
        $actualData = $service->fetchFrequencyByAssembly(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByDocument()
    {
        $service = new Vote();
        $service->setDriver($this->pdo);
        $expectedData = [(new VoteModel())
            ->setVoteId(7)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setDocumentId(2)];
        $actualData = $service->fetchByDocument(1, 2, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $vote = (new VoteModel())
            ->setVoteId(8)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(2);

        $expectedTable = $this->createArrayDataSet([
            'Vote' => [
                ['vote_id' => 8, 'issue_id' => 2, 'assembly_id' => 1, 'document_id' => 2],
            ],
        ])->getTable('Vote');
        $actualTable = $this->getConnection()->createQueryTable(
            'Vote',
            'SELECT `vote_id`, `issue_id`, `assembly_id`, `document_id` FROM Vote WHERE vote_id = 8'
        );

        $voteService = new Vote();
        $voteService->setDriver($this->pdo);
        $voteService->create($vote);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $vote = (new VoteModel())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new \DateTime('2001-01-01 00:00:00'));

        $expectedTable = $this->createArrayDataSet([
            'Vote' => [
                ['vote_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'document_id' => 1, 'date' => '2001-01-01 00:01:00'],
            ],
        ])->getTable('Vote');
        $actualTable = $this->getConnection()->createQueryTable(
            'Vote',
            'SELECT `vote_id`, `issue_id`, `assembly_id`, `document_id`, `date` FROM Vote WHERE vote_id = 1'
        );

        $voteService = new Vote();
        $voteService->setDriver($this->pdo);
        $voteService->update($vote);

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
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
            ],
            'Issue' => [
                ['assembly_id' => 1, 'issue_id' => 1],
                ['assembly_id' => 1, 'issue_id' => 2],
                ['assembly_id' => 1, 'issue_id' => 3],
                ['assembly_id' => 2, 'issue_id' => 1],
                ['assembly_id' => 2, 'issue_id' => 2],
                ['assembly_id' => 2, 'issue_id' => 3],
            ],
            'Document' => [
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 2, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 3, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 4, 'issue_id' => 2, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
            ],
            'Vote' => [
                ['vote_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'document_id' => 1, 'date' => '2000-01-01'],
                ['vote_id' => 2, 'issue_id' => 1, 'assembly_id' => 1, 'document_id' => 2, 'date' => '2000-02-01'],
                ['vote_id' => 3, 'issue_id' => 2, 'assembly_id' => 1, 'document_id' => 1],
                ['vote_id' => 4, 'issue_id' => 2, 'assembly_id' => 1, 'document_id' => 1],
                ['vote_id' => 5, 'issue_id' => 2, 'assembly_id' => 1, 'document_id' => 1],
                ['vote_id' => 6, 'issue_id' => 2, 'assembly_id' => 1, 'document_id' => 1],
                ['vote_id' => 7, 'issue_id' => 2, 'assembly_id' => 1, 'document_id' => 2],
            ]
        ]);
    }
}
