<?php

namespace Althingi\Service;

use PHPUnit\Framework\TestCase;
use Althingi\DatabaseConnection;
use Althingi\Service\{President, Assembly};
use Althingi\Model\Assembly as AssemblyModel;
use Althingi\Events\{UpdateEvent, AddEvent};
use Psr\EventDispatcher\EventDispatcherInterface;
use Mockery;
use PDO;

class AssemblyTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $expectedData = (new AssemblyModel())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'));

        $actualData = $assemblyService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetNotFound()
    {
        $assemblyService = new President();
        $assemblyService->setDriver($this->pdo);

        $actualData = $assemblyService->get(100);

        $this->assertNull($actualData);
    }

    public function testFetch()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [
            (new AssemblyModel())->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(2)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(3)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(4)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(5)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(6)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(7)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(8)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(9)->setFrom(new \DateTime('2000-01-01')),
        ];
        $actualData = $assemblyService->fetchAll();

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchSubsetFromZero()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [
            (new AssemblyModel())->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(2)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(3)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(4)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(5)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(6)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(7)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(8)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(9)->setFrom(new \DateTime('2000-01-01')),
        ];
        $actualData = $assemblyService->fetchAll(0);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchSubsetFromFive()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [
            (new AssemblyModel())->setAssemblyId(6)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(7)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(8)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(9)->setFrom(new \DateTime('2000-01-01')),
        ];
        $actualData = $assemblyService->fetchAll(5);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchSubset()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [
            (new AssemblyModel())->setAssemblyId(6)->setFrom(new \DateTime('2000-01-01')),
            (new AssemblyModel())->setAssemblyId(7)->setFrom(new \DateTime('2000-01-01')),
        ];
        $actualData = $assemblyService->fetchAll(5, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchEmpty()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [];
        $actualData = $assemblyService->fetchAll(10, 25);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function($args) {
                return $args instanceof AddEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(10)
            ->setFrom(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 4, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 5, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 6, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 7, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 8, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 9, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 10, 'from' => '2000-01-01', 'to' => null],
            ],
        ])->getTable('Assembly');
        $actualTable = $this->getConnection()->createQueryTable('Assembly', 'SELECT * FROM Assembly');

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->create($assembly);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSaveUpdate()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 4, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 5, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 6, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 7, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 8, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 9, 'from' => '2000-01-01', 'to' => null],
            ],
        ])->getTable('Assembly');
        $actualTable = $this->getConnection()->createQueryTable('Assembly', 'SELECT * FROM Assembly');

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $affectedRows = $assemblyService->save($assembly);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(0, $affectedRows);
    }

    public function testSaveCreate()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof AddEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(10)
            ->setFrom(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 4, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 5, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 6, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 7, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 8, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 9, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 10, 'from' => '2000-01-01', 'to' => null],
            ],
        ])->getTable('Assembly');
        $actualTable = $this->getConnection()->createQueryTable('Assembly', 'SELECT * FROM Assembly');

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $affectedRows = $assemblyService->save($assembly);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    public function testUpdate()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(new \DateTime('2000-02-01'));

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->update($assembly);

        $expectedTable = $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => '2000-02-01'],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 4, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 5, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 6, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 7, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 8, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 9, 'from' => '2000-01-01', 'to' => null],
            ],
        ])->getTable('Assembly');
        $queryTable = $this->getConnection()->createQueryTable('Assembly', 'SELECT * FROM Assembly');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testCount()
    {
        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo);

        $this->assertEquals(9, $assemblyService->count());
    }

    public function testCreateEventFiredRowsOne()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(10)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(null);


        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->create($assembly);
    }

    public function testUpdateFireEventRowsOne()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(new \DateTime('2000-01-01'));

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->update($assembly);
    }
    public function testUpdateFireEventRowsZero()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(null);

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->update($assembly);
    }

    public function testSaveFireAddEventOne()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(10)
            ->setFrom(new \DateTime('2000-01-01'));

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->save($assembly);
    }

    public function testSaveFireUpdateEventTwo()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(new \DateTime('2000-01-01'));

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->save($assembly);
    }

    public function testSaveFireUpdateEventZero()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $assembly = (new AssemblyModel())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(null);

        $assemblyService = new Assembly();
        $assemblyService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->save($assembly);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 4, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 5, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 6, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 7, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 8, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 9, 'from' => '2000-01-01', 'to' => null],
            ],
        ]);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
