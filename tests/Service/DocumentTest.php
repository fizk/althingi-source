<?php

namespace Althingi\Service;

use Althingi\Service\Document;
use Althingi\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Document as DocumentModel;
use PDO;

class DocumentTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $service = new Document();
        $service->setDriver($this->pdo);

        $expectedData = (new DocumentModel())
            ->setDocumentId(1)
            ->setAssemblyId(1)
            ->setCategory('A')
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
            ->setDocumentId(5)
            ->setIssueId(1)
            ->setCategory('A')
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->pdo);
        $documentService->create($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 5, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
            ]
        ])->getTable('Document');
        $queryTable = $this->getConnection()
            ->createQueryTable('Document', 'SELECT * FROM Document');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testSave()
    {
        $document = (new DocumentModel())
            ->setAssemblyId(1)
            ->setDocumentId(5)
            ->setIssueId(1)
            ->setCategory('A')
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->pdo);
        $documentService->save($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 5, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
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
            ->setCategory('A')
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('thisismytype')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->pdo);
        $documentService->update($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'thisismytype'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'category' => 'A', 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
            ]
        ])->getTable('Document');
        $queryTable = $this->getConnection()->createQueryTable('Document', 'SELECT * FROM Document');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null]
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1, 'category' => 'A' ,],
                ['issue_id' => 2, 'assembly_id' => 1, 'category' => 'A' ,],
            ],
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'category' => 'A' ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'category' => 'A' ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'category' => 'A' ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'category' => 'A' ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
            ]
        ]);
    }
}
