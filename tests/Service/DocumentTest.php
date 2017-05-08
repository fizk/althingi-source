<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */
namespace Althingi\Service;

use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\Model\Document as DocumentModel;

class DocumentTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $service = new Document();
        $service->setDriver($this->pdo);

        $expectedData = (new DocumentModel())
            ->setDocumentId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');
        $actualData = $service->get(1, 1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByIssue()
    {
        $service = new Document();
        $service->setDriver($this->pdo);

        $documents = $service->fetchByIssue(1, 2);

        $this->assertCount(1, $documents);
        $this->assertInstanceOf(DocumentModel::class, $documents[0]);
    }

    public function testCreate()
    {
        $document = (new DocumentModel())
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->pdo);
        $documentService->create($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 2, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 3, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 4, 'issue_id' => 2, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 5, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
            ]
        ])->getTable('Document');
        $queryTable = $this->getConnection()->createQueryTable('Document', 'SELECT * FROM Document');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testUpdate()
    {
        $document = (new DocumentModel())
            ->setDocumentId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('thisismytype')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->pdo);
        $documentService->update($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'thisismytype'],
                ['document_id' => 2, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 3, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 4, 'issue_id' => 2, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
            ]
        ])->getTable('Document');
        $queryTable = $this->getConnection()->createQueryTable('Document', 'SELECT * FROM Document');

        $this->assertTablesEqual($expectedTable, $queryTable);
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
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null]
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1],
                ['issue_id' => 2, 'assembly_id' => 1],
            ],
            'Document' => [
                ['document_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 2, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 3, 'issue_id' => 1, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
                ['document_id' => 4, 'issue_id' => 2, 'assembly_id' => 1, 'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'],
            ]
        ]);
    }
}