<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use DateTime;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class VoteTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getVoteSuccess()
    {
        $service = new Vote();
        $service->setDriver($this->getPDO());

        $expectedData = (new Model\Vote())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setDate(new \DateTime('2000-01-01T00:01:00'));
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function voteNotFound()
    {
        $service = new Vote();
        $service->setDriver($this->getPDO());

        $expectedData = null;
        $actualData = $service->get(10000);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByIssue()
    {
        $service = new Vote();
        $service->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Vote())
                ->setVoteId(1)
                ->setIssueId(1)
                ->setKind(Model\KindEnum::A)
                ->setAssemblyId(1)
                ->setDocumentId(1)
                ->setDate(new \DateTime('2000-01-01T00:01:00')),
            (new Model\Vote())
                ->setVoteId(2)
                ->setIssueId(1)
                ->setKind(Model\KindEnum::A)
                ->setAssemblyId(1)
                ->setDocumentId(2)
                ->setDate(new \DateTime('2000-02-01')),
        ];

        $actualData = $service->fetchByIssue(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchDateFrequencyByIssue()
    {
        $service = new Vote();
        $service->setDriver($this->getPDO());

        $expectedData = [
            (new Model\DateAndCount())->setCount(1)->setDate(new \DateTime('2000-01-01')),
            (new Model\DateAndCount())->setCount(1)->setDate(new \DateTime('2000-02-01')),
        ];
        $actualData = $service->fetchDateFrequencyByIssue(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchFrequencyByAssembly()
    {
        $service = new Vote();
        $service->setDriver($this->getPDO());

        $expectedData = [
            (new Model\DateAndCount())->setCount(5),
            (new Model\DateAndCount())->setCount(1)->setDate(new \DateTime('2000-01-01')),
            (new Model\DateAndCount())->setCount(1)->setDate(new \DateTime('2000-02-01')),
        ];
        $actualData = $service->fetchFrequencyByAssembly(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByDocument()
    {
        $service = new Vote();
        $service->setDriver($this->getPDO());
        $expectedData = [(new Model\Vote())
            ->setVoteId(7)
            ->setIssueId(2)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setDocumentId(2)];
        $actualData = $service->fetchByDocument(1, 2, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        $vote = (new Model\Vote())
            ->setVoteId(9)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
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
        $voteService->setDriver($this->getPDO());
        $voteService->create($vote);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $vote = (new Model\Vote())
            ->setVoteId(9)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
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
        $voteService->setDriver($this->getPDO());
        $voteService->save($vote);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function updateSuccess()
    {
        $vote = (new Model\Vote())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
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
        $voteService->setDriver($this->getPDO());
        $voteService->update($vote);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function countByAssembly()
    {
        $voteService = new Vote();
        $voteService->setDriver($this->getPDO());

        $expectedCount = 7;
        $actualCount = $voteService->countByAssembly(1);

        $this->assertEquals($expectedCount, $actualCount);
    }

    #[Test]
    public function getFrequencyByAssemblyAndCongressman()
    {
        $voteService = new Vote();
        $voteService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\VoteTypeAndCount())->setVote('ja')->setCount(1),
            (new Model\VoteTypeAndCount())->setVote('nei')->setCount(1),
        ];
        $actualData = $voteService->getFrequencyByAssemblyAndCongressman(1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getFrequencyByAssemblyAndCongressmanWithDate()
    {
        $voteService = new Vote();
        $voteService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\VoteTypeAndCount())->setVote('ja')->setCount(1),
            (new Model\VoteTypeAndCount())->setVote('nei')->setCount(1),
        ];
        $actualData = $voteService->getFrequencyByAssemblyAndCongressman(1, 2, new \DateTime('2000-01-01'));

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createFireEventResourceCreated()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $vote = (new Model\Vote())
            ->setVoteId(9)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(2);

        (new Vote())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->create($vote);
    }

    #[Test]
    public function updateFireEventResourceFoundNoUpdateNeeded()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $vote = (new Model\Vote())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-01T00:00:00'));

        $statement = $this->getPDO()->prepare('select * from Vote');
        $statement->execute();

        (new Vote())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($vote);
    }

    #[Test]
    public function updateFireEventResourceFoundUpdatedRequired()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $vote = (new Model\Vote())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-02T00:00:00'));

        (new Vote())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($vote);
    }

    #[Test]
    public function saveFireEventResourceCreated()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $vote = (new Model\Vote())
            ->setVoteId(100)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-02T00:00:00'));

        (new Vote())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($vote);
    }

    #[Test]
    public function saveFireEventResourceFoundNoUpdatedNeeded()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $vote = (new Model\Vote())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-01T00:00:00'));

        (new Vote())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($vote);
    }

    #[Test]
    public function saveFireEventResourceFoundUpdateRequired()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $vote = (new Model\Vote())
            ->setVoteId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
            ->setYes(0)
            ->setNo(0)
            ->setInaction(0)
            ->setDocumentId(1)
            ->setDate(new DateTime('2000-01-02T00:00:00'));

        (new Vote())
            ->setDriver($this->getPDO())
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
                ['assembly_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value],
                ['assembly_id' => 1, 'issue_id' => 2, 'kind' => Model\KindEnum::A->value],
                ['assembly_id' => 1, 'issue_id' => 3, 'kind' => Model\KindEnum::A->value],
                ['assembly_id' => 2, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value],
                ['assembly_id' => 2, 'issue_id' => 2, 'kind' => Model\KindEnum::A->value],
                ['assembly_id' => 2, 'issue_id' => 3, 'kind' => Model\KindEnum::A->value],
            ],
            'Document' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type'
                ], [
                    'document_id' => 2,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type'
                ], [
                    'document_id' => 3,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00',
                    'url' => 'http://url.com',
                    'type' => 'type'
                ], [
                    'document_id' => 4,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
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
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'document_id' => 1,
                    'date' => '2000-01-01T00:01:00'
                ], [
                    'vote_id' => 2,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'document_id' => 2,
                    'date' => '2000-02-01'
                ],[
                    'vote_id' => 3,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'document_id' => 1
                ],[
                    'vote_id' => 4,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'document_id' => 1
                ],[
                    'vote_id' => 5,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'document_id' => 1
                ],[
                    'vote_id' => 6,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'document_id' => 1
                ],[
                    'vote_id' => 7,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'document_id' => 2
                ],[
                    'vote_id' => 8,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 2,
                    'document_id' => 2
                ],
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
