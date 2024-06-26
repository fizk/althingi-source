<?php

namespace Althingi\Service;

use Althingi\Service\Document;
use Althingi\DatabaseConnection;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Document as DocumentModel;
use Althingi\Model\KindEnum;
use Mockery;
use PDO;
use Psr\EventDispatcher\EventDispatcherInterface;

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
            ->setKind(KindEnum::A)
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
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->pdo);
        $documentService->create($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 5, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
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
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->pdo);
        $documentService->save($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 5, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
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
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('thisismytype')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->pdo);
        $documentService->update($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'thisismytype'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
            ]
        ])->getTable('Document');
        $queryTable = $this->getConnection()->createQueryTable('Document', 'SELECT * FROM Document');

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

        $document = (new DocumentModel())
            ->setAssemblyId(1)
            ->setDocumentId(5)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        (new Document())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->create($document);
    }

    public function testUpdateFireEventZeroResourceFoundButNoUpdateRequired()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $document = (new DocumentModel())
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        (new Document())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($document);
    }

    public function testUpdateFireEventOneResourceFoundAndAnUpdateRequired()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $document = (new DocumentModel())
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com/add-to-url');

        (new Document())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($document);
    }

    public function testSaveFireEventZeroResourceFoundButNoUpdateRequired()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $document = (new DocumentModel())
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        (new Document())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($document);
    }

    public function testSaveFireEventOneNeedsToBeCreated()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $document = (new DocumentModel())
            ->setAssemblyId(1)
            ->setDocumentId(5)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        (new Document())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($document);
    }

    public function testSaveFireEventTwoCresourceFoundAndNeedsToBeUpdated()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $document = (new DocumentModel())
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com/update');

        (new Document())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($document);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null]
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => KindEnum::A->value ,],
                ['issue_id' => 2, 'assembly_id' => 1, 'kind' => KindEnum::A->value ,],
            ],
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => KindEnum::A->value ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'kind' => KindEnum::A->value ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'kind' => KindEnum::A->value ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'kind' => KindEnum::A->value ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
            ]
        ]);
    }
}
