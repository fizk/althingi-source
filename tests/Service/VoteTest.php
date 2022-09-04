<?php

namespace Althingi\Service;

use Althingi\Model\Vote as VoteModel;
use Althingi\Model\VoteTypeAndCount;
use Althingi\Service\Vote;
use Althingi\DatabaseConnection;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use PHPUnit\Framework\TestCase;
use Althingi\Model\DateAndCount as DateAndCountModel;
use DateTime;
use Mockery;
use PDO;
use Psr\EventDispatcher\EventDispatcherInterface;

class VoteTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testVote()
    {
        $service = new Vote();
        $service->setDriver($this->pdo);

        $expectedData = (new VoteModel())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setCategory('A')
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setDate(new \DateTime('2000-01-01T00:01:00'));
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
                ->setCategory('A')
                ->setAssemblyId(1)
                ->setDocumentId(1)
                ->setDate(new \DateTime('2000-01-01T00:01:00')),
            (new VoteModel())
                ->setVoteId(2)
                ->setIssueId(1)
                ->setCategory('A')
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
            ->setCategory('A')
            ->setAssemblyId(1)
            ->setDocumentId(2)];
        $actualData = $service->fetchByDocument(1, 2, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $vote = (new VoteModel())
            ->setVoteId(9)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(2);

        $expectedTable = $this->createArrayDataSet([
            'Vote' => [
                ['vote_id' => 9, 'issue_id' => 2, 'assembly_id' => 1, 'document_id' => 2],
            ],
        ])->getTable('Vote');
        $actualTable = $this->getConnection()->createQueryTable(
            'Vote',
            'SELECT `vote_id`, `issue_id`, `assembly_id`, `document_id` FROM Vote WHERE vote_id = 9'
        );

        $voteService = new Vote();
        $voteService->setDriver($this->pdo);
        $voteService->create($vote);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $vote = (new VoteModel())
            ->setVoteId(9)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(2);

        $expectedTable = $this->createArrayDataSet([
            'Vote' => [
                ['vote_id' => 9, 'issue_id' => 2, 'assembly_id' => 1, 'document_id' => 2],
            ],
        ])->getTable('Vote');
        $actualTable = $this->getConnection()->createQueryTable(
            'Vote',
            'SELECT `vote_id`, `issue_id`, `assembly_id`, `document_id` FROM Vote WHERE vote_id = 9'
        );

        $voteService = new Vote();
        $voteService->setDriver($this->pdo);
        $voteService->save($vote);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $vote = (new VoteModel())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new \DateTime('2001-01-01 00:00:00'));

        $expectedTable = $this->createArrayDataSet([
            'Vote' => [
                [
                    'vote_id' => 1,
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'document_id' => 1,
                    'date' => '2001-01-01 00:01:00'
                ],
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

    public function testCountByAssembly()
    {
        $voteService = new Vote();
        $voteService->setDriver($this->pdo);

        $expectedCount = 7;
        $actualCount = $voteService->countByAssembly(1);

        $this->assertEquals($expectedCount, $actualCount);
    }

    public function testGetFrequencyByAssemblyAndCongressman()
    {
        $voteService = new Vote();
        $voteService->setDriver($this->pdo);

        $expectedData = [
            (new VoteTypeAndCount())->setVote('ja')->setCount(1),
            (new VoteTypeAndCount())->setVote('nei')->setCount(1),
        ];
        $actualData = $voteService->getFrequencyByAssemblyAndCongressman(1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetFrequencyByAssemblyAndCongressmanWithDate()
    {
        $voteService = new Vote();
        $voteService->setDriver($this->pdo);

        $expectedData = [
            (new VoteTypeAndCount())->setVote('ja')->setCount(1),
            (new VoteTypeAndCount())->setVote('nei')->setCount(1),
        ];
        $actualData = $voteService->getFrequencyByAssemblyAndCongressman(1, 2, new \DateTime('2000-01-01'));

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreateFireEventResourceCreated()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $vote = (new VoteModel())
            ->setVoteId(9)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(2);

        (new Vote())
            ->setDriver($this->pdo)
            ->setEventDispatcher(($eventDispatcher))
            ->create($vote);
    }

    public function testUpdateFireEventResourceFoundNoUpdateNeeded()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $vote = (new VoteModel())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-01T00:00:00'));

        $statement = $this->pdo->prepare('select * from Vote');
        $statement->execute();

        (new Vote())
            ->setDriver($this->pdo)
            ->setEventDispatcher(($eventDispatcher))
            ->update($vote);
    }

    public function testUpdateFireEventResourceFoundUpdatedRequired()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $vote = (new VoteModel())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-02T00:00:00'));

        (new Vote())
            ->setDriver($this->pdo)
            ->setEventDispatcher(($eventDispatcher))
            ->update($vote);
    }

    public function testSaveFireEventResourceCreated()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $vote = (new VoteModel())
            ->setVoteId(100)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-02T00:00:00'));

        (new Vote())
            ->setDriver($this->pdo)
            ->setEventDispatcher(($eventDispatcher))
            ->save($vote);
    }

    public function testSaveFireEventResourceFoundNoUpdatedNeeded()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $vote = (new VoteModel())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-01T00:00:00'));

        (new Vote())
            ->setDriver($this->pdo)
            ->setEventDispatcher(($eventDispatcher))
            ->save($vote);
    }

    public function testSaveFireEventResourceFoundUpdateRequired()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $vote = (new VoteModel())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-02T00:00:00'));

        (new Vote())
            ->setDriver($this->pdo)
            ->setEventDispatcher(($eventDispatcher))
            ->save($vote);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name1', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 2, 'name' => 'name2', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name3', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 4, 'name' => 'name4', 'birth' => '2000-01-01', 'death' => null],
            ],
            'Issue' => [
                ['assembly_id' => 1, 'issue_id' => 1, 'category' => 'A'],
                ['assembly_id' => 1, 'issue_id' => 2, 'category' => 'A'],
                ['assembly_id' => 1, 'issue_id' => 3, 'category' => 'A'],
                ['assembly_id' => 2, 'issue_id' => 1, 'category' => 'A'],
                ['assembly_id' => 2, 'issue_id' => 2, 'category' => 'A'],
                ['assembly_id' => 2, 'issue_id' => 3, 'category' => 'A'],
            ],
            'Document' => [
                ['document_id' => 1,
                    'issue_id' => 1,
                    'category' => 'A',
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type'
                ], [
                    'document_id' => 2,
                    'issue_id' => 1,
                    'category' => 'A',
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type'
                ], [
                    'document_id' => 3,
                    'issue_id' => 1,
                    'category' => 'A',
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type'
                ], [
                    'document_id' => 4,
                    'issue_id' => 2,
                    'category' => 'A',
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type'
                ],
            ],
            'Vote' => [
                [
                    'vote_id' => 1,
                    'issue_id' => 1,
                    'category' => 'A',
                    'assembly_id' => 1,
                    'document_id' => 1,
                    'date' => '2000-01-01T00:01:00'
                ], [
                    'vote_id' => 2,
                    'issue_id' => 1,
                    'category' => 'A',
                    'assembly_id' => 1,
                    'document_id' => 2,
                    'date' => '2000-02-01'
                ],
                ['vote_id' => 3, 'issue_id' => 2, 'category' => 'A', 'assembly_id' => 1, 'document_id' => 1],
                ['vote_id' => 4, 'issue_id' => 2, 'category' => 'A', 'assembly_id' => 1, 'document_id' => 1],
                ['vote_id' => 5, 'issue_id' => 2, 'category' => 'A', 'assembly_id' => 1, 'document_id' => 1],
                ['vote_id' => 6, 'issue_id' => 2, 'category' => 'A', 'assembly_id' => 1, 'document_id' => 1],
                ['vote_id' => 7, 'issue_id' => 2, 'category' => 'A', 'assembly_id' => 1, 'document_id' => 2],
                ['vote_id' => 8, 'issue_id' => 2, 'category' => 'A', 'assembly_id' => 2, 'document_id' => 2],
            ],
            'VoteItem' => [
                ['vote_id' => 1, 'congressman_id' => 1, 'vote' => 'ja', 'vote_item_id' => 1],
                ['vote_id' => 1, 'congressman_id' => 2, 'vote' => 'ja', 'vote_item_id' => 2],
                ['vote_id' => 2, 'congressman_id' => 1, 'vote' => 'ja', 'vote_item_id' => 3],
                ['vote_id' => 2, 'congressman_id' => 2, 'vote' => 'nei', 'vote_item_id' => 4],
            ]
        ]);
    }
}
