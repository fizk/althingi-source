<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{AddEvent, UpdateEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class CongressmanDocumentTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $congressmanDocumentService = new CongressmanDocument();
        $congressmanDocumentService->setDriver($this->getPDO());

        $expectedData = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(1);
        $actualData = $congressmanDocumentService->get(1, 1, 1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        $congressman = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(2)
            ->setOrder(2);

        $expectedTable = $this->createArrayDataSet([
            'Document_has_Congressman' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1, 'congressman_id' => 1, 'minister' => null, 'order' => 1
                ], [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1, 'congressman_id' => 2, 'minister' => null, 'order' => 2
                ],
            ],
        ])->getTable('Document_has_Congressman');
        $actualTable = $this->getConnection()
            ->createQueryTable('Document_has_Congressman', 'SELECT * FROM Document_has_Congressman');

        $congressmanService = new CongressmanDocument();
        $congressmanService->setDriver($this->getPDO());
        $congressmanService->create($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $congressman = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(2)
            ->setOrder(2);

        $expectedTable = $this->createArrayDataSet([
            'Document_has_Congressman' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'congressman_id' => 1, 'minister' => null, 'order' => 1
                ], [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value, 'assembly_id' => 1,
                    'congressman_id' => 2, 'minister' => null, 'order' => 2
                ],
            ],
        ])->getTable('Document_has_Congressman');
        $actualTable = $this->getConnection()
            ->createQueryTable('Document_has_Congressman', 'SELECT * FROM Document_has_Congressman');

        $congressmanService = new CongressmanDocument();
        $congressmanService->setDriver($this->getPDO());
        $congressmanService->save($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function updateSuccess()
    {
        $congressman = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setMinister('hello')
            ->setOrder(2);

        $expectedTable = $this->createArrayDataSet([
            'Document_has_Congressman' => [
                [
                    'document_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value ,'assembly_id' => 1,
                    'congressman_id' => 1, 'minister' => 'hello', 'order' => 2
                ]
            ],
        ])->getTable('Document_has_Congressman');
        $actualTable = $this->getConnection()
            ->createQueryTable('Document_has_Congressman', 'SELECT * FROM Document_has_Congressman');

        $congressmanService = new CongressmanDocument();
        $congressmanService->setDriver($this->getPDO());
        $congressmanService->update($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createFireEventOne()
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

        $congressman = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(2)
            ->setOrder(2);

        (new CongressmanDocument())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($congressman);
    }

    #[Test]
    public function updateFireEventOne()
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

        $congressman = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(2);

        (new CongressmanDocument())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($congressman);
    }

    #[Test]
    public function updateFireEventZero()
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

        $congressman = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(1);

        (new CongressmanDocument())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($congressman);
    }

    #[Test]
    public function saveFireEventZero()
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

        $congressman = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(1);

        (new CongressmanDocument())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($congressman);
    }

    #[Test]
    public function saveFireEventOne()
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

        $congressman = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(1)
            ->setOrder(2);

        (new CongressmanDocument())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($congressman);
    }

    #[Test]
    public function saveFireEventTwo()
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

        $congressman = (new Model\CongressmanDocument())
            ->setDocumentId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(1)
            ->setCongressmanId(2)
            ->setOrder(1);

        (new CongressmanDocument())
            ->setDriver($this->getPDO())
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
                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => Model\KindEnum::A->value]
            ],
            'Document' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
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
                    'kind' => Model\KindEnum::A->value,
                    'assembly_id' => 1,
                    'congressman_id' => 1,
                    'order' => 1
                ]
            ],
        ]);
    }
}
