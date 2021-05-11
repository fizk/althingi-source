<?php

namespace Althingi\Service;

use Althingi\Model\VoteItem as VoteItemModel;
use Althingi\Model\VoteItemAndAssemblyIssue as VoteItemAndAssemblyIssueModel;
use Althingi\Service\VoteItem;
use Althingi\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use PDO;

class VoteItemTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

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
                ['vote_item_id' => 1, 'vote_id' => 1, 'congressman_id' => 1, 'vote' => 'ja'],
                ['vote_item_id' => 2, 'vote_id' => 1, 'congressman_id' => 2, 'vote' => 'ja'],
                ['vote_item_id' => 5, 'vote_id' => 1, 'congressman_id' => 3, 'vote' => 'ja'],
            ],
        ])->getTable('VoteItem');
        $actualTable = $this->getConnection()->createQueryTable('VoteItem', 'SELECT * FROM VoteItem WHERE vote_id = 1');

        $voteItemService = new VoteItem();
        $voteItemService->setDriver($this->pdo);
        $voteItemService->create($voteItem);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testCreateAlreadyExist()
    {
        $voteItem = (new VoteItemModel())
            ->setVoteId(1)
            ->setCongressmanId(1)
            ->setVoteItemId(5)
            ->setVote('ja');

        $voteItemService = new VoteItem();
        $voteItemService->setDriver($this->pdo);
        try {
            $voteItemService->create($voteItem);
        } catch (\PDOException $e) {
            $this->assertEquals(1062, $e->errorInfo[1]);
        }
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
                ['vote_item_id' => 1, 'vote_id' => 1, 'congressman_id' => 1, 'vote' => 'nei'],
            ],
        ])->getTable('VoteItem');
        $actualTable = $this->getConnection()
            ->createQueryTable('VoteItem', 'SELECT * FROM VoteItem WHERE vote_item_id = 1');

        $voteItemService = new VoteItem();
        $voteItemService->setDriver($this->pdo);
        $voteItemService->update($voteItem);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
            ],
            'Issue' => [
                ['assembly_id' => 1, 'issue_id' => 1, 'category' => 'A'],
                ['assembly_id' => 1, 'issue_id' => 2, 'category' => 'A'],
                ['assembly_id' => 1, 'issue_id' => 3, 'category' => 'A'],
                ['assembly_id' => 2, 'issue_id' => 1, 'category' => 'A'],
                ['assembly_id' => 2, 'issue_id' => 2, 'category' => 'A'],
                ['assembly_id' => 2, 'issue_id' => 3, 'category' => 'A'],
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => '', 'birth' => '2001-01-01'],
                ['congressman_id' => 2, 'name' => '', 'birth' => '2001-01-01'],
                ['congressman_id' => 3, 'name' => '', 'birth' => '2001-01-01'],
            ],
            'Document' => [
                ['document_id' => 1,
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type',
                    'category' => 'A'
                ], [
                    'document_id' => 2,
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type',
                    'category' => 'A'
                ], [
                    'document_id' => 3,
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type',
                    'category' => 'A'
                ], [
                    'document_id' => 4,
                    'issue_id' => 2,
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type',
                    'category' => 'A'
                ],
            ],
            'Vote' => [
                [
                    'vote_id' => 1,
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'document_id' => 1,
                    'date' => '2000-01-01',
                    'category' => 'A'
                ], [
                    'vote_id' => 2,
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'document_id' => 2,
                    'date' => '2000-02-01',
                    'category' => 'A'
                ],
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
