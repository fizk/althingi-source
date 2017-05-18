<?php

namespace Althingi\Service;

use Althingi\DatabaseConnection;
use Althingi\Model\CongressmanDocument as CongressmanDocumentModel;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_TestCase;

class CongressmanDocumentTest extends PHPUnit_Extensions_Database_TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $congressmanDocumentService = new CongressmanDocument();
        $congressmanDocumentService->setDriver($this->pdo);

        $expectedData = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(1);
        $actualData = $congressmanDocumentService->get(1, 1, 1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCongressmanId(2)
            ->setOrder(2);

        $expectedTable = $this->createArrayDataSet([
            'Document_has_Congressman' => [
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'congressman_id' => 1, 'minister' => null, 'order' => 1],
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'congressman_id' => 2, 'minister' => null, 'order' => 2],
            ],
        ])->getTable('Document_has_Congressman');
        $actualTable = $this->getConnection()->createQueryTable('Document_has_Congressman', 'SELECT * FROM Document_has_Congressman');

        $congressmanService = new CongressmanDocument();
        $congressmanService->setDriver($this->pdo);
        $congressmanService->create($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setMinister('hello')
            ->setOrder(2);

        $expectedTable = $this->createArrayDataSet([
            'Document_has_Congressman' => [
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'congressman_id' => 1, 'minister' => 'hello', 'order' => 2]
            ],
        ])->getTable('Document_has_Congressman');
        $actualTable = $this->getConnection()->createQueryTable('Document_has_Congressman', 'SELECT * FROM Document_has_Congressman');

        $congressmanService = new CongressmanDocument();
        $congressmanService->setDriver($this->pdo);
        $congressmanService->update($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name1', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 2, 'name' => 'name2', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name3', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 4, 'name' => 'name4', 'birth' => '2000-01-01', 'death' => null],
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1]
            ],
            'Document' => [
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01', 'url' => '', 'type' => '']
            ],
            'Document_has_Congressman' => [
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'congressman_id' => 1, 'order' => 1]
            ],
        ]);
    }
}
