<?php

namespace AlthingiTest\Service;

use Althingi\Model\AssemblyStatus;
use Althingi\Model\IssueTypeStatus;
use Althingi\Service\Issue;
use Althingi\ServiceEvents\ServiceEventsListener;
use AlthingiTest\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Issue as IssueModel;
use Althingi\Model\IssueAndDate as IssueAndDateModel;
use Psr\Log\NullLogger;
use \AlthingiTest\ElasticBlackHoleClient;
use Zend\EventManager\EventManager;

class IssueTest extends TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

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
            ->setCategory('A')
            ->setDate(new \DateTime('2000-01-01'));
        $this->assertEquals($expectedDataWithDate, $actualDataWithDate);
    }

    public function testGetB()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $expectedDataWithDate = $service->getWithDate(1, 1, 'B');
        $actualDataWithDate = (new IssueAndDateModel())
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCongressmanId(null)
            ->setType(null)
            ->setTypeSubname(null)
            ->setStatus(null)
            ->setCategory('B')
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

        $issues = $service->fetchByAssembly(1, 0, 25, null, [], [], ['B']);

        $this->assertCount(1, $issues);
        $this->assertInstanceOf(IssueAndDateModel::class, $issues[0]);
    }

    public function testGetByAssemblyBandA()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $issues = $service->fetchByAssembly(1, 0, 25, null, [], [], ['A', 'B']);

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
    public function testFetchByCongressmanAndAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $issues = $service->fetchByAssemblyAndCongressman(1, 1);
        $this->assertCount(0, $issues);
    }

    public function testFetchStateByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);
        $statuses = $service->fetchStateByAssembly(1);

        $this->assertCount(2, $statuses);
        $this->assertEquals(2, $statuses[0]->getCount());
        $this->assertInstanceOf(AssemblyStatus::class, $statuses[0]);
    }

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
        $serviceEventListener = (new ServiceEventsListener())
                ->setElasticSearchClient(new ElasticBlackHoleClient())
                ->setLogger(new NullLogger());
        $eventManager = new EventManager();
        $serviceEventListener->attach($eventManager);

        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setCategory('A');

        $issueService = new Issue();
        $issueService->setDriver($this->pdo);
        $issueService->setEventManager($eventManager);
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
        $serviceEventListener = (new ServiceEventsListener())
            ->setElasticSearchClient(new ElasticBlackHoleClient())
            ->setLogger(new NullLogger());

        $eventManager = new EventManager();
        $serviceEventListener->attach($eventManager);

        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(4)
            ->setType('ab')
            ->setCategory('A');

        $issueService = new Issue();
        $issueService->setDriver($this->pdo);
        $issueService->setEventManager($eventManager);
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
        $serviceEventListener = (new ServiceEventsListener())
            ->setElasticSearchClient(new ElasticBlackHoleClient())
            ->setLogger(new NullLogger());

        $eventManager = new EventManager();
        $serviceEventListener->attach($eventManager);

        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(3)
            ->setCategory('A')
            ->setStatus('awesome');

        $issueService = new Issue();
        $issueService->setDriver($this->pdo);
        $issueService->setEventManager($eventManager);
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
                    'category' => 'A'
                ],
            ],
        ])->getTable('Issue');
        $queryTable = $this->getConnection()->createQueryTable(
            'Issue',
            'SELECT `issue_id`, `assembly_id`, `congressman_id`, `type`, `status`, `type_subname`, `category` 
              FROM Issue
              WHERE issue_id = 3 AND assembly_id = 1'
        );

        $this->assertTablesEqual($expectedTable, $queryTable);
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
                    'category' => 'A',
                    'congressman_id' => 1,
                    'type' => 'l',
                    'status' => 'some',
                    'type_subname' => 'something'
                ], [
                    'issue_id' => 2,
                    'assembly_id' => 1,
                    'category' => 'A',
                    'type' => 'l',
                    'status' => 'some',
                    'type_subname' => 'stjórnarfrumvarp'
                ],
                ['issue_id' => 3, 'assembly_id' => 1, 'category' => 'A'],
                ['issue_id' => 1, 'assembly_id' => 2, 'category' => 'A'],


                ['issue_id' => 1, 'assembly_id' => 1, 'category' => 'B'],
                ['issue_id' => 1, 'assembly_id' => 2, 'category' => 'B'],
            ],
            'Document' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'category' => 'A',
                    'assembly_id' => 1,
                    'date' => '2000-01-01',
                    'url' => '',
                    'type' => ''
                ], [
                    'document_id' => 2,
                    'issue_id' => 1,
                    'category' => 'A',
                    'assembly_id' => 1,
                    'date' => '2000-01-02',
                    'url' => '',
                    'type' => ''
                ], [
                    'document_id' => 3,
                    'issue_id' => 1,
                    'category' => 'A',
                    'assembly_id' => 1,
                    'date' => '2000-01-03',
                    'url' => '',
                    'type' => ''
                ],
            ],
        ]);
    }
}
