<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */
namespace Althingi\Service;

use Althingi\Model\VoteItem as VoteItemModel;
use Althingi\Model\VoteItemAndAssemblyIssue as VoteItemAndAssemblyIssueModel;
use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;

class VoteItemTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $service = new VoteItem();
        $service->setDriver($this->pdo);

        $expectedData = (new VoteItemModel())
            ->setVoteId(1)
            ->setVoteItemId(1)
            ->setCongressmanId(1)
            ->setVote('ja');

        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetNotFound()
    {
        $service = new VoteItem();
        $service->setDriver($this->pdo);

        $expectedData = null;
        $actualData = $service->get(1000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByVote()
    {
        $service = new VoteItem();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new VoteItemModel())->setVoteId(1)->setVoteItemId(1)->setCongressmanId(1)->setVote('ja'),
            (new VoteItemModel())->setVoteId(1)->setVoteItemId(2)->setCongressmanId(2)->setVote('ja'),
        ];
        $actualData = $service->fetchByVote(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByVoteNotFound()
    {
        $service = new VoteItem();
        $service->setDriver($this->pdo);

        $expectedData = [];
        $actualData = $service->fetchByVote(1000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetByVote()
    {
        $service = new VoteItem();
        $service->setDriver($this->pdo);

        $expectedData = (new VoteItemAndAssemblyIssueModel())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setVoteId(1)
            ->setVoteItemId(1)
            ->setCongressmanId(1)
            ->setVote('ja');
        $actualData = $service->getByVote(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetByVoteNotFound()
    {
        $service = new VoteItem();
        $service->setDriver($this->pdo);

        $expectedData = null;
        $actualData = $service->getByVote(1, 10000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $voteItem = (new VoteItemModel())
            ->setVoteId(1)
            ->setCongressmanId(3)
            ->setVoteItemId(5)
            ->setVote('ja');

        $expectedTable = $this->createArrayDataSet([
            'VoteItem' => [
                ['vote_id' => 1, 'congressman_id' => 1, 'vote' => 'ja', 'vote_item_id' => 1],
                ['vote_id' => 1, 'congressman_id' => 2, 'vote' => 'ja', 'vote_item_id' => 2],
                ['vote_id' => 1, 'congressman_id' => 3, 'vote' => 'ja', 'vote_item_id' => 5],
            ],
        ])->getTable('VoteItem');
        $actualTable = $this->getConnection()->createQueryTable('VoteItem', 'SELECT * FROM VoteItem WHERE vote_id = 1');

        $voteItemService = new VoteItem();
        $voteItemService->setDriver($this->pdo);
        $voteItemService->create($voteItem);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $voteItem = (new VoteItemModel())
            ->setVoteId(1)
            ->setCongressmanId(1)
            ->setVoteItemId(1)
            ->setVote('nei');

        $expectedTable = $this->createArrayDataSet([
            'VoteItem' => [
                ['vote_id' => 1, 'congressman_id' => 1, 'vote' => 'nei', 'vote_item_id' => 1],
            ],
        ])->getTable('VoteItem');
        $actualTable = $this->getConnection()
            ->createQueryTable('VoteItem', 'SELECT * FROM VoteItem WHERE vote_item_id = 1');

        $voteItemService = new VoteItem();
        $voteItemService->setDriver($this->pdo);
        $voteItemService->update($voteItem);

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
            'Congressman' => [
                ['congressman_id' => 1, 'name' => '', 'birth' => ''],
                ['congressman_id' => 2, 'name' => '', 'birth' => ''],
                ['congressman_id' => 3, 'name' => '', 'birth' => ''],
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
            ],
            'VoteItem' => [
                ['vote_id' => 1, 'congressman_id' => 1, 'vote' => 'ja', 'vote_item_id' => 1],
                ['vote_id' => 1, 'congressman_id' => 2, 'vote' => 'ja', 'vote_item_id' => 2],
                ['vote_id' => 2, 'congressman_id' => 1, 'vote' => 'ja', 'vote_item_id' => 3],
                ['vote_id' => 2, 'congressman_id' => 2, 'vote' => 'ja', 'vote_item_id' => 4],
            ]
        ]);
    }
}
