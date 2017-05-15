<?php

namespace Althingi\Service;

use Althingi\DatabaseConnection;
use Althingi\Model\AssemblyStatus;
use Althingi\Model\IssueTypeStatus;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\Model\Issue as IssueModel;
use Althingi\Model\IssueAndDate as IssueAndDateModel;

class IssueTest extends PHPUnit_Extensions_Database_TestCase
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
            ->setDate(new \DateTime('2000-01-01'));
        $this->assertEquals($expectedDataWithDate, $actualDataWithDate);

        $expectedData = $service->getWithDate(1, 2);
        $actualData = (new IssueAndDateModel())
            ->setIssueId(1)
            ->setAssemblyId(2);
        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetByAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $issues = $service->fetchByAssembly(1, 0, 25);

        $this->assertCount(3, $issues);
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

    public function testFetchByCongressmanAndAssembly()
    {
        $service = new Issue();
        $service->setDriver($this->pdo);

        $issues = $service->fetchByAssemblyAndCongressman(1, 1);
        $this->assertCount(1, $issues);
        $this->assertInstanceOf(IssueModel::class, $issues[0]);
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
        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(4);

        $issueService = new Issue();
        $issueService->setDriver($this->pdo);
        $issueService->create($issue);

        $expectedTable = $this->createArrayDataSet([
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1, 'congressman_id' => 1, 'type' => 'l', 'status' => 'some', 'type_subname' => 'something'],
                ['issue_id' => 1, 'assembly_id' => 2],
                ['issue_id' => 2, 'assembly_id' => 1, 'type' => 'l', 'status' => 'some', 'type_subname' => 'stjórnarfrumvarp'],
                ['issue_id' => 3, 'assembly_id' => 1],
                ['issue_id' => 4, 'assembly_id' => 1],
            ],
        ])->getTable('Issue');
        $queryTable = $this->getConnection()->createQueryTable('Issue', 'SELECT `issue_id`, `assembly_id`, `congressman_id`, `type`, `status`, `type_subname` FROM Issue');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testUpdate()
    {
        $issue = (new IssueModel())
            ->setAssemblyId(1)
            ->setIssueId(3)
            ->setStatus('awesome');

        $issueService = new Issue();
        $issueService->setDriver($this->pdo);
        $issueService->update($issue);

        $expectedTable = $this->createArrayDataSet([
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1, 'congressman_id' => 1, 'type' => 'l', 'status' => 'some', 'type_subname' => 'something'],
                ['issue_id' => 1, 'assembly_id' => 2],
                ['issue_id' => 2, 'assembly_id' => 1, 'type' => 'l', 'status' => 'some', 'type_subname' => 'stjórnarfrumvarp'],
                ['issue_id' => 3, 'assembly_id' => 1, 'status' => 'awesome'],
            ],
        ])->getTable('Issue');
        $queryTable = $this->getConnection()->createQueryTable('Issue', 'SELECT `issue_id`, `assembly_id`, `congressman_id`, `type`, `status`, `type_subname` FROM Issue');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
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
                ['issue_id' => 1, 'assembly_id' => 1, 'congressman_id' => 1, 'type' => 'l', 'status' => 'some', 'type_subname' => 'something'],
                ['issue_id' => 2, 'assembly_id' => 1, 'type' => 'l', 'status' => 'some', 'type_subname' => 'stjórnarfrumvarp'],
                ['issue_id' => 3, 'assembly_id' => 1],
                ['issue_id' => 1, 'assembly_id' => 2],
            ],
            'Document' => [
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01', 'url' => '', 'type' => ''],
                ['document_id' => 2, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-02', 'url' => '', 'type' => ''],
                ['document_id' => 3, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-03', 'url' => '', 'type' => ''],
            ],
        ]);
    }
}
