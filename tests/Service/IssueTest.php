<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{AddEvent, UpdateEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class IssueTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());

        $expectedDataWithDate = $service->getWithDate(1, 1);
        $actualDataWithDate = (new Model\IssueAndDate())
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setType('l')
            ->setTypeSubname('something')
            ->setStatus('some')
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'));
        $this->assertEquals($expectedDataWithDate, $actualDataWithDate);
    }

    #[Test]
    public function getB()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());

        $expectedDataWithDate = $service->getWithDate(1, 1, Model\KindEnum::B);
        $actualDataWithDate = (new Model\IssueAndDate())
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCongressmanId(null)
            ->setType(null)
            ->setTypeSubname(null)
            ->setStatus(null)
            ->setKind(Model\KindEnum::B)
            ->setDate(null);
        $this->assertEquals($expectedDataWithDate, $actualDataWithDate);
    }

    #[Test]
    public function getByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());

        $issues = $service->fetchByAssembly(1, 0, 25);

        $this->assertCount(3, $issues);
        $this->assertInstanceOf(Model\IssueAndDate::class, $issues[0]);
    }

    #[Test]
    public function getByAssemblyType()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());

        $issues = $service->fetchByAssembly(1, 0, 25, null, ['l']);

        $this->assertCount(2, $issues);
        $this->assertInstanceOf(Model\IssueAndDate::class, $issues[0]);
    }

    #[Test]
    public function getByAssemblyB()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());

        $issues = $service->fetchByAssembly(1, 0, 25, null, [], [], [Model\KindEnum::B]);

        $this->assertCount(1, $issues);
        $this->assertInstanceOf(Model\IssueAndDate::class, $issues[0]);
    }

    #[Test]
    public function getByAssemblyBandA()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());

        $issues = $service->fetchByAssembly(1, 0, 25, null, [], [], [Model\KindEnum::A, Model\KindEnum::B]);

        $this->assertCount(4, $issues);
        $this->assertInstanceOf(Model\IssueAndDate::class, $issues[0]);
    }

    #[Test]
    public function fetchByCongressman()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());

        $issues = $service->fetchByCongressman(1);
        $this->assertCount(1, $issues);
        $this->assertInstanceOf(Model\Issue::class, $issues[0]);
    }

    /**
     * @todo this needs a bit more work
     */
    // public function testFetchByCongressmanAndAssembly()
    // {
    //     $service = new Issue();
    //     $service->setDriver($this->getPDO());

    //     $issues = $service->fetchByAssemblyAndCongressman(1, 1);
    //     $this->assertCount(0, $issues);
    // }

    //@todo fixme
//    public function testFetchStateByAssembly()
//    {
//        $service = new Issue();
//        $service->setDriver($this->getPDO());
//        $statuses = $service->fetchStateByAssembly(1);
//
//        $this->assertCount(2, $statuses);
//        $this->assertEquals(2, $statuses[0]->getCount());
//        $this->assertInstanceOf(AssemblyStatus::class, $statuses[0]);
//    }

    #[Test]
    public function fetchBillStatisticsByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());
        $statuses = $service->fetchBillStatisticsByAssembly(1);

        $this->assertCount(1, $statuses);
        $this->assertEquals(2, $statuses[0]->getCount());
        $this->assertEquals('some', $statuses[0]->getStatus());
        $this->assertInstanceOf(Model\IssueTypeStatus::class, $statuses[0]);
    }

    #[Test]
    public function fetchNonGovernmentBillStatisticsByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());
        $statuses = $service->fetchNonGovernmentBillStatisticsByAssembly(1);

        $this->assertCount(1, $statuses);
        $this->assertEquals(1, $statuses[0]->getCount());
        $this->assertEquals('some', $statuses[0]->getStatus());
        $this->assertInstanceOf(Model\IssueTypeStatus::class, $statuses[0]);
    }

    #[Test]
    public function fetchGovernmentBillStatisticsByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());
        $statuses = $service->fetchGovernmentBillStatisticsByAssembly(1);

        $this->assertCount(1, $statuses);
        $this->assertEquals(1, $statuses[0]->getCount());
        $this->assertEquals('some', $statuses[0]->getStatus());
        $this->assertInstanceOf(Model\IssueTypeStatus::class, $statuses[0]);
    }

    #[Test]
    public function countByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->getPDO());
        $count = $service->countByAssembly(1);
        $this->assertEquals(3, $count);
    }

    #[Test]
    public function createSuccess()
    {
        $issue = (new Model\Issue())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setKind(Model\KindEnum::A)
        ;

        $issueService = new Issue();
        $issueService->setDriver($this->getPDO());
        $issueService->create($issue);

        $expectedTable = $this->createArrayDataSet([
            'Issue' => [
                [
                    'issue_id' => 4,
                    'assembly_id' => 1,
                    'congressman_id' => null,
                    'type' => null,
                    'status' => null,
                    'type_subname' => null
                ],
            ],
        ])->getTable('Issue');
        $queryTable = $this->getConnection()->createQueryTable(
            'Issue',
            'SELECT `issue_id`, `assembly_id`, `congressman_id`, `type`, `status`, `type_subname`
                FROM Issue
                WHERE issue_id = 4 AND assembly_id = 1'
        );

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $issue = (new Model\Issue())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setType('ab')
            ->setKind(Model\KindEnum::A)
        ;

        $issueService = new Issue();
        $issueService->setDriver($this->getPDO());
        $issueService->save($issue);

        $expectedTable = $this->createArrayDataSet([
            'Issue' => [
                [
                    'issue_id' => 4,
                    'assembly_id' => 1,
                    'congressman_id' => null,
                    'type' => 'ab',
                    'status' => null,
                    'type_subname' => null
                ],
            ],
        ])->getTable('Issue');
        $queryTable = $this->getConnection()->createQueryTable(
            'Issue',
            'SELECT `issue_id`, `assembly_id`, `congressman_id`, `type`, `status`, `type_subname`
              FROM Issue
              WHERE issue_id = 4 AND assembly_id = 1'
        );

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function updateSuccess()
    {
        $issue = (new Model\Issue())
            ->setAssemblyId(1)
            ->setIssueId(3)
            ->setKind(Model\KindEnum::A)
            ->setStatus('awesome');

        $issueService = new Issue();
        $issueService->setDriver($this->getPDO());
        $issueService->update($issue);

        $expectedTable = $this->createArrayDataSet([
            'Issue' => [
                [
                    'issue_id' => 3,
                    'assembly_id' => 1,
                    'congressman_id' => null,
                    'type' => null,
                    'status' => 'awesome',
                    'type_subname' => null,
                    'kind' => Model\KindEnum::A->value
                ],
            ],
        ])->getTable('Issue');
        $queryTable = $this->getConnection()->createQueryTable(
            'Issue',
            'SELECT `issue_id`, `assembly_id`, `congressman_id`, `type`, `status`, `type_subname`, `kind`
              FROM Issue
              WHERE issue_id = 3 AND assembly_id = 1'
        );

        $this->assertTablesEqual($expectedTable, $queryTable);
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

        $issue = (new Model\Issue())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setKind(Model\KindEnum::A)
        ;

        (new Issue())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($issue);
    }

    #[Test]
    public function updateFireEventResourceFoundButNoUpdateRequired()
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

        $issue = (new Model\Issue())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something')
        ;

        (new Issue())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($issue);
    }

    #[Test]
    public function updateFireEventResourceFoundAndUpdateIsNeeded()
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

        $issue = (new Model\Issue())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something-update')
        ;

        (new Issue())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($issue);
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

        $issue = (new Model\Issue())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something-update')
        ;

        (new Issue())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($issue);
    }

    #[Test]
    public function saveFireEventResourceFoundNoUpdateRequired()
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

        $issue = (new Model\Issue())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something')
        ;

        (new Issue())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($issue);
    }

    #[Test]
    public function saveFireEventResourceFoundUpdateNeeded()
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

        $issue = (new Model\Issue())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something-update')
        ;

        (new Issue())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($issue);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'Name', 'birth' => '1978-01-11']
            ],
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
            ],
            'Issue' => [
                [
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'type' => 'l',
                    'status' => 'some',
                    'type_subname' => 'something'
                ], [
                    'issue_id' => 2,
                    'assembly_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'type' => 'l',
                    'status' => 'some',
                    'type_subname' => 'stjórnarfrumvarp'
                ],
                ['issue_id' => 3, 'assembly_id' => 1, 'kind' => Model\KindEnum::A->value],
                ['issue_id' => 1, 'assembly_id' => 2, 'kind' => Model\KindEnum::A->value],


                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => Model\KindEnum::B->value],
                ['issue_id' => 1, 'assembly_id' => 2, 'kind' => Model\KindEnum::B->value],
            ],
            'Document' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-01',
                    'url' => '',
                    'type' => 'stjórnarfrumvarp'
                ], [
                    'document_id' => 2,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-02',
                    'url' => '',
                    'type' => ''
                ], [
                    'document_id' => 3,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-03',
                    'url' => '',
                    'type' => ''
                ],
            ],
        ]);
    }
}
