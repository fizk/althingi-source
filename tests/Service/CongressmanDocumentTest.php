<?php

namespace Althingi\Service;

use PHPUnit\Framework\TestCase;
use Althingi\Model\CongressmanDocument as CongressmanDocumentModel;
use Althingi\Service\CongressmanDocument;
use Althingi\DatabaseConnection;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Model\KindEnum;
use Psr\EventDispatcher\EventDispatcherInterface;
use Mockery;
use PDO;

class CongressmanDocumentTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $congressmanDocumentService = new CongressmanDocument();
        $congressmanDocumentService->setDriver($this->pdo);

        $expectedData = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
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
            ->setKind(KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(2)
            ->setOrder(2);

        $expectedTable = $this->createArrayDataSet([
            'Document_has_Congressman' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => KindEnum::A->value,
                    'assembly_id' => 1, 'congressman_id' => 1, 'minister' => null, 'order' => 1
                ], [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => KindEnum::A->value,
                    'assembly_id' => 1, 'congressman_id' => 2, 'minister' => null, 'order' => 2
                ],
            ],
        ])->getTable('Document_has_Congressman');
        $actualTable = $this->getConnection()
            ->createQueryTable('Document_has_Congressman', 'SELECT * FROM Document_has_Congressman');

        $congressmanService = new CongressmanDocument();
        $congressmanService->setDriver($this->pdo);
        $congressmanService->create($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(2)
            ->setOrder(2);

        $expectedTable = $this->createArrayDataSet([
            'Document_has_Congressman' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'congressman_id' => 1, 'minister' => null, 'order' => 1
                ], [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => KindEnum::A->value, 'assembly_id' => 1,
                    'congressman_id' => 2, 'minister' => null, 'order' => 2
                ],
            ],
        ])->getTable('Document_has_Congressman');
        $actualTable = $this->getConnection()
            ->createQueryTable('Document_has_Congressman', 'SELECT * FROM Document_has_Congressman');

        $congressmanService = new CongressmanDocument();
        $congressmanService->setDriver($this->pdo);
        $congressmanService->save($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setMinister('hello')
            ->setOrder(2);

        $expectedTable = $this->createArrayDataSet([
            'Document_has_Congressman' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => KindEnum::A->value ,'assembly_id' => 1,
                    'congressman_id' => 1, 'minister' => 'hello', 'order' => 2
                ]
            ],
        ])->getTable('Document_has_Congressman');
        $actualTable = $this->getConnection()
            ->createQueryTable('Document_has_Congressman', 'SELECT * FROM Document_has_Congressman');

        $congressmanService = new CongressmanDocument();
        $congressmanService->setDriver($this->pdo);
        $congressmanService->update($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testCreateFireEventOne()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(2)
            ->setOrder(2);

        (new CongressmanDocument())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->create($congressman);
    }

    public function testUpdateFireEventOne()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(2);

        (new CongressmanDocument())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($congressman);
    }
    public function testUpdateFireEventZero()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(1);

        (new CongressmanDocument())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($congressman);
    }

    public function testSaveFireEventZero()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(1);

        (new CongressmanDocument())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($congressman);
    }

    public function testSaveFireEventOne()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(2);

        (new CongressmanDocument())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($congressman);
    }

    public function testSaveFireEventTwo()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $congressman = (new CongressmanDocumentModel())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(2)
            ->setOrder(1);

        (new CongressmanDocument())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($congressman);
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
                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => KindEnum::A->value]
            ],
            'Document' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'kind' => KindEnum::A->value,
                    'assembly_id' => 1,
                    'date' => '2000-01-01',
                    'url' => '',
                    'type' => ''
                ]
            ],
            'Document_has_Congressman' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'kind' => KindEnum::A->value,
                    'assembly_id' => 1,
                    'congressman_id' => 1,
                    'order' => 1
                ]
            ],
        ]);
    }
}
