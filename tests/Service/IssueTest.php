<?php

namespace Althingi\Service;

use Althingi\Model\IssueTypeStatus;
use Althingi\Service\Issue;
use Althingi\DatabaseConnection;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Issue as IssueModel;
use Althingi\Model\IssueAndDate as IssueAndDateModel;
use Althingi\Model\KindEnum;
use Mockery;
use PDO;
use Psr\EventDispatcher\EventDispatcherInterface;

class IssueTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $expectedDataWithDate = $service->getWithDate(1, 1);
        $actualDataWithDate = (new IssueAndDateModel())
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setType('l')
            ->setTypeSubname('something')
            ->setStatus('some')
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'));
        $this->assertEquals($expectedDataWithDate, $actualDataWithDate);
    }

    public function testGetB()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $expectedDataWithDate = $service->getWithDate(1, 1, KindEnum::B);
        $actualDataWithDate = (new IssueAndDateModel())
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCongressmanId(null)
            ->setType(null)
            ->setTypeSubname(null)
            ->setStatus(null)
            ->setKind(KindEnum::B)
            ->setDate(null);
        $this->assertEquals($expectedDataWithDate, $actualDataWithDate);
    }

    public function testGetByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $issues = $service->fetchByAssembly(1, 0, 25);

        $this->assertCount(3, $issues);
        $this->assertInstanceOf(IssueAndDateModel::class, $issues[0]);
    }

    public function testGetByAssemblyType()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $issues = $service->fetchByAssembly(1, 0, 25, null, ['l']);

        $this->assertCount(2, $issues);
        $this->assertInstanceOf(IssueAndDateModel::class, $issues[0]);
    }

    public function testGetByAssemblyB()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $issues = $service->fetchByAssembly(1, 0, 25, null, [], [], [KindEnum::B]);

        $this->assertCount(1, $issues);
        $this->assertInstanceOf(IssueAndDateModel::class, $issues[0]);
    }

    public function testGetByAssemblyBandA()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $issues = $service->fetchByAssembly(1, 0, 25, null, [], [], [KindEnum::A, KindEnum::B]);

        $this->assertCount(4, $issues);
        $this->assertInstanceOf(IssueAndDateModel::class, $issues[0]);
    }

    public function testFetchByCongressman()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $issues = $service->fetchByCongressman(1);
        $this->assertCount(1, $issues);
        $this->assertInstanceOf(IssueModel::class, $issues[0]);
    }

    /**
     * @todo this needs a bit more work
     */
    // public function testFetchByCongressmanAndAssembly()
    // {
    //     $service = new Issue();
    //     $service->setDriver($this->pdo);

    //     $issues = $service->fetchByAssemblyAndCongressman(1, 1);
    //     $this->assertCount(0, $issues);
    // }

    //@todo fixme
//    public function testFetchStateByAssembly()
//    {
//        $service = new Issue();
//        $service->setDriver($this->pdo);
//        $statuses = $service->fetchStateByAssembly(1);
//
//        $this->assertCount(2, $statuses);
//        $this->assertEquals(2, $statuses[0]->getCount());
//        $this->assertInstanceOf(AssemblyStatus::class, $statuses[0]);
//    }

    public function testFetchBillStatisticsByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);
        $statuses = $service->fetchBillStatisticsByAssembly(1);

        $this->assertCount(1, $statuses);
        $this->assertEquals(2, $statuses[0]->getCount());
        $this->assertEquals('some', $statuses[0]->getStatus());
        $this->assertInstanceOf(IssueTypeStatus::class, $statuses[0]);
    }

    public function testFetchNonGovernmentBillStatisticsByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);
        $statuses = $service->fetchNonGovernmentBillStatisticsByAssembly(1);

        $this->assertCount(1, $statuses);
        $this->assertEquals(1, $statuses[0]->getCount());
        $this->assertEquals('some', $statuses[0]->getStatus());
        $this->assertInstanceOf(IssueTypeStatus::class, $statuses[0]);
    }

    public function testFetchGovernmentBillStatisticsByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);
        $statuses = $service->fetchGovernmentBillStatisticsByAssembly(1);

        $this->assertCount(1, $statuses);
        $this->assertEquals(1, $statuses[0]->getCount());
        $this->assertEquals('some', $statuses[0]->getStatus());
        $this->assertInstanceOf(IssueTypeStatus::class, $statuses[0]);
    }

    public function testCountByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);
        $count = $service->countByAssembly(1);
        $this->assertEquals(3, $count);
    }

    public function testCreate()
    {
        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setKind(KindEnum::A)
        ;

        $issueService = new Issue();
        $issueService->setDriver($this->pdo);
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

    public function testSave()
    {
        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setType('ab')
            ->setKind(KindEnum::A)
        ;

        $issueService = new Issue();
        $issueService->setDriver($this->pdo);
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

    public function testUpdate()
    {
        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(3)
            ->setKind(KindEnum::A)
            ->setStatus('awesome');

        $issueService = new Issue();
        $issueService->setDriver($this->pdo);
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
                    'kind' => KindEnum::A->value
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

        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setKind(KindEnum::A)
        ;

        (new Issue())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->create($issue);
    }

    public function testUpdateFireEventResourceFoundButNoUpdateRequired()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something')
        ;

        (new Issue())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($issue);
    }

    public function testUpdateFireEventResourceFoundAndUpdateIsNeeded()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something-update')
        ;

        (new Issue())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($issue);
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

        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setKind(KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something-update')
        ;

        (new Issue())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($issue);
    }

    public function testSaveFireEventResourceFoundNoUpdateRequired()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something')
        ;

        (new Issue())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($issue);
    }

    public function testSaveFireEventResourceFoundUpdateNeeded()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setCongressmanId(1)
            ->setType('l')
            ->setStatus('some')
            ->setTypeSubname('something-update')
        ;

        (new Issue())
            ->setDriver($this->pdo)
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
                    'kind' => KindEnum::A->value,
                    'congressman_id' => 1,
                    'type' => 'l',
                    'status' => 'some',
                    'type_subname' => 'something'
                ], [
                    'issue_id' => 2,
                    'assembly_id' => 1,
                    'kind' => KindEnum::A->value,
                    'type' => 'l',
                    'status' => 'some',
                    'type_subname' => 'stjórnarfrumvarp'
                ],
                ['issue_id' => 3, 'assembly_id' => 1, 'kind' => KindEnum::A->value],
                ['issue_id' => 1, 'assembly_id' => 2, 'kind' => KindEnum::A->value],


                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => KindEnum::B->value],
                ['issue_id' => 1, 'assembly_id' => 2, 'kind' => KindEnum::B->value],
            ],
            'Document' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'kind' => KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-01',
                    'url' => '',
                    'type' => 'stjórnarfrumvarp'
                ], [
                    'document_id' => 2,
                    'issue_id' => 1,
                    'kind' => KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-02',
                    'url' => '',
                    'type' => ''
                ], [
                    'document_id' => 3,
                    'issue_id' => 1,
                    'kind' => KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-03',
                    'url' => '',
                    'type' => ''
                ],
            ],
        ]);
    }
}
