<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{AddEvent, UpdateEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\{Test};
use Psr\EventDispatcher\EventDispatcherInterface;

class DocumentTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $service = new Document();
        $service->setDriver($this->getPDO());

        $expectedData = (new Model\Document())
            ->setDocumentId(1)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
            ->setIssueId(1)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');
        $actualData = $service->get(1, 1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByIssue()
    {
        $service = new Document();
        $service->setDriver($this->getPDO());

        $documents = $service->fetchByIssue(1, 2);

        $this->assertCount(1, $documents);
        $this->assertInstanceOf(Model\Document::class, $documents[0]);
    }

    #[Test]
    public function createSuccess()
    {
        $document = (new Model\Document())
            ->setAssemblyId(1)
            ->setDocumentId(5)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->getPDO());
        $documentService->create($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 5, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
            ]
        ])->getTable('Document');
        $queryTable = $this->getConnection()
            ->createQueryTable('Document', 'SELECT * FROM Document');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $document = (new Model\Document())
            ->setAssemblyId(1)
            ->setDocumentId(5)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->getPDO());
        $documentService->save($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 5, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
            ]
        ])->getTable('Document');
        $queryTable = $this->getConnection()->createQueryTable('Document', 'SELECT * FROM Document');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function updateSuccess()
    {
        $document = (new Model\Document())
            ->setDocumentId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('thisismytype')
            ->setUrl('http://url.com');

        $documentService = new Document();
        $documentService->setDriver($this->getPDO());
        $documentService->update($document);

        $expectedTable = $this->createArrayDataSet([
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'thisismytype'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
            ]
        ])->getTable('Document');
        $queryTable = $this->getConnection()->createQueryTable('Document', 'SELECT * FROM Document');

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

        $document = (new Model\Document())
            ->setAssemblyId(1)
            ->setDocumentId(5)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        (new Document())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($document);
    }

    #[Test]
    public function updateFireEventZeroResourceFoundButNoUpdateRequired()
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

        $document = (new Model\Document())
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        (new Document())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($document);
    }

    #[Test]
    public function updateFireEventOneResourceFoundAndAnUpdateRequired()
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

        $document = (new Model\Document())
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com/add-to-url');

        (new Document())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($document);
    }

    #[Test]
    public function saveFireEventZeroResourceFoundButNoUpdateRequired()
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

        $document = (new Model\Document())
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        (new Document())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($document);
    }

    #[Test]
    public function saveFireEventOneNeedsToBeCreated()
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

        $document = (new Model\Document())
            ->setAssemblyId(1)
            ->setDocumentId(5)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com');

        (new Document())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($document);
    }

    #[Test]
    public function saveFireEventTwoCresourceFoundAndNeedsToBeUpdated()
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

        $document = (new Model\Document())
            ->setAssemblyId(1)
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setDate(new \DateTime('2000-01-01'))
            ->setType('type')
            ->setUrl('http://url.com/update');

        (new Document())
            ->setDriver($this->getPDO())
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
                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => Model\KindEnum::A->value ,],
                ['issue_id' => 2, 'assembly_id' => 1, 'kind' => Model\KindEnum::A->value ,],
            ],
            'Document' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 2, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 3, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ], [
                    'document_id' => 4, 'issue_id' => 2, 'kind' => Model\KindEnum::A->value ,'assembly_id' => 1,
                    'date' => '2000-01-01 00:00:00', 'url' => 'http://url.com', 'type' => 'type'
                ],
            ]
        ]);
    }
}
