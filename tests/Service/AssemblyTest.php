<?php

namespace Althingi\Service;

use Althingi\{Model, Service};
use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Mockery;
use PHPUnit\Framework\Attributes\{Test, After};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class AssemblyTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[After]
    public function down(): void
    {
        Mockery::close();
    }

    #[Test]
    public function getSuccessfull()
    {
        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO());

        $expectedData = (new Model\Assembly())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'));

        $actualData = $assemblyService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getNotFound()
    {
        $assemblyService = new Service\President();
        $assemblyService->setDriver($this->getPDO());

        $actualData = $assemblyService->get(100);

        $this->assertNull($actualData);
    }

    #[Test]
    public function fetch()
    {
        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Assembly())->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(2)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(3)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(4)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(5)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(6)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(7)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(8)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(9)->setFrom(new \DateTime('2000-01-01')),
        ];
        $actualData = $assemblyService->fetchAll();

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchSubsetFromZero()
    {
        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Assembly())->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(2)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(3)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(4)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(5)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(6)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(7)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(8)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(9)->setFrom(new \DateTime('2000-01-01')),
        ];
        $actualData = $assemblyService->fetchAll(0);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchSubsetFromFive()
    {
        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Assembly())->setAssemblyId(6)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(7)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(8)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(9)->setFrom(new \DateTime('2000-01-01')),
        ];
        $actualData = $assemblyService->fetchAll(5);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchSubset()
    {
        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Assembly())->setAssemblyId(6)->setFrom(new \DateTime('2000-01-01')),
            (new Model\Assembly())->setAssemblyId(7)->setFrom(new \DateTime('2000-01-01')),
        ];
        $actualData = $assemblyService->fetchAll(5, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchEmpty()
    {
        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO());

        $expectedData = [];
        $actualData = $assemblyService->fetchAll(10, 25);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function create()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof AddEvent;
            })
            ->getMock();

        $assembly = (new Model\Assembly())
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

        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->create($assembly);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveUpdate()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $assembly = (new Model\Assembly())
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

        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $affectedRows = $assemblyService->save($assembly);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(0, $affectedRows);
    }

    #[Test]
    public function saveCreate()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof AddEvent;
            })
            ->getMock();

        $assembly = (new Model\Assembly())
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

        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $affectedRows = $assemblyService->save($assembly);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    #[Test]
    public function update()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $assembly = (new Model\Assembly())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(new \DateTime('2000-02-01'));

        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
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

    #[Test]
    public function countSuccess()
    {
        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO());

        $this->assertEquals(9, $assemblyService->count());
    }

    #[Test]
    public function createEventFiredRowsOne()
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

        $assembly = (new Model\Assembly())
            ->setAssemblyId(10)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(null);


        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->create($assembly);
    }

    #[Test]
    public function updateFireEventRowsOne()
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

        $assembly = (new Model\Assembly())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(new \DateTime('2000-01-01'));

        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->update($assembly);
    }
    public function testUpdateFireEventRowsZero()
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

        $assembly = (new Model\Assembly())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(null);

        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->update($assembly);
    }

    #[Test]
    public function saveFireAddEventOne()
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

        $assembly = (new Model\Assembly())
            ->setAssemblyId(10)
            ->setFrom(new \DateTime('2000-01-01'));

        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->save($assembly);
    }

    #[Test]
    public function saveFireUpdateEventTwo()
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

        $assembly = (new Model\Assembly())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(new \DateTime('2000-01-01'));

        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $assemblyService->save($assembly);
    }

    #[Test]
    public function saveFireUpdateEventZero()
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

        $assembly = (new Model\Assembly())
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(null);

        $assemblyService = new Service\Assembly();
        $assemblyService->setDriver($this->getPDO())
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
}
