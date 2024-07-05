<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use DateTime;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class PlenaryTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->getPDO());

        $expectedData = (new Model\Plenary())
            ->setPlenaryId(1)
            ->setFrom(new \DateTime('2000-01-01 00:00:00'))
            ->setAssemblyId(1);

        $actualData = $plenaryService->get(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getNotFound()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->getPDO());

        $expectedData = null;

        $actualData = $plenaryService->get(1, 100);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByAssembly()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Plenary())->setPlenaryId(1)->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-01')),
            (new Model\Plenary())->setPlenaryId(2)->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-01')),
            (new Model\Plenary())->setPlenaryId(3)
                ->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01'))
                ->setTo(new \DateTime('2001-01-01')),
            (new Model\Plenary())->setPlenaryId(4)
                ->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01'))
                ->setTo(new \DateTime('2001-01-01'))->setName('p-name'),
        ];

        $actualData = $plenaryService->fetchByAssembly(1, 0, 20);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByAssemblyNotFound()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->getPDO());

        $expectedData = [];

        $actualData = $plenaryService->fetchByAssembly(100, 0, 20);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function countByAssembly()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->getPDO());

        $expectedData = 4;
        $actualData = $plenaryService->countByAssembly(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function countByAssemblyNotFound()
    {
        $plenaryService = new Plenary();
        $plenaryService->setDriver($this->getPDO());

        $expectedData = 0;
        $actualData = $plenaryService->countByAssembly(100);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(5);

        $assemblyService = new Plenary();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->create($plenary);

        $expectedTable = $this->createArrayDataSet([
            'Plenary' => [
                ['plenary_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 2, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                [
                    'plenary_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'plenary_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
                ['plenary_id' => 5, 'assembly_id' => 1, 'from' => null, 'to' => null, 'name' => null],
            ],
        ])->getTable('Plenary');
        $queryTable = $this->getConnection()->createQueryTable('Plenary', 'SELECT * FROM Plenary');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function createNegative()
    {
        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(-5);

        $assemblyService = new Plenary();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->create($plenary);

        $expectedTable = $this->createArrayDataSet([
            'Plenary' => [
                ['plenary_id' => -5, 'assembly_id' => 1, 'from' => null, 'to' => null, 'name' => null],
                ['plenary_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 2, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                [
                    'plenary_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'plenary_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
            ],
        ])->getTable('Plenary');
        $queryTable = $this->getConnection()->createQueryTable('Plenary', 'SELECT * FROM Plenary');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(5);

        $assemblyService = new Plenary();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->save($plenary);

        $expectedTable = $this->createArrayDataSet([
            'Plenary' => [
                ['plenary_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 2, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                [
                    'plenary_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'plenary_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
                ['plenary_id' => 5, 'assembly_id' => 1, 'from' => null, 'to' => null, 'name' => null],
            ],
        ])->getTable('Plenary');
        $queryTable = $this->getConnection()->createQueryTable('Plenary', 'SELECT * FROM Plenary');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function saveNegative()
    {
        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(-5);

        $assemblyService = new Plenary();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->save($plenary);

        $expectedTable = $this->createArrayDataSet([
            'Plenary' => [
                ['plenary_id' => -5, 'assembly_id' => 1, 'from' => null, 'to' => null, 'name' => null],
                ['plenary_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 2, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                [
                    'plenary_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'plenary_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
            ],
        ])->getTable('Plenary');
        $queryTable = $this->getConnection()->createQueryTable('Plenary', 'SELECT * FROM Plenary');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function updateSuccess()
    {
        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(1)
            ->setFrom(new \DateTime('2000-01-01 00:00:00'))
            ->setName('NewName');

        $assemblyService = new Plenary();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->update($plenary);

        $expectedTable = $this->createArrayDataSet([
            'Plenary' => [
                [
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => 'NewName'
                ], [
                    'plenary_id' => 2,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => null
                ], [
                    'plenary_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'plenary_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
            ],
        ])->getTable('Plenary');
        $queryTable = $this->getConnection()->createQueryTable('Plenary', 'SELECT * FROM Plenary');

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

        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(5);

        (new Plenary())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->create($plenary)
        ;
    }

    #[Test]
    public function updateFireEventResourceFoundNoNeedForAnUpdate()
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

        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(1)
            ->setFrom(new DateTime('2000-01-01'));

        (new Plenary())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($plenary)
        ;
    }

    #[Test]
    public function updateFireEventResourceFoundUpdateRequired()
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

        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(1)
            ->setFrom(new DateTime('2001-01-01'));

        (new Plenary())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($plenary)
        ;
    }

    #[Test]
    public function saveFireEventResourceCreated()
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

        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(10)
            ->setFrom(new DateTime('2001-01-01'));

        (new Plenary())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($plenary)
        ;
    }

    #[Test]
    public function saveFireEventResourceFoundNoUpdate()
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

        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(1)
            ->setFrom(new DateTime('2000-01-01'));

        (new Plenary())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($plenary)
        ;
    }

    #[Test]
    public function saveFireEventResourceFoundUpdateRequired()
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

        $plenary = (new Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(1)
            ->setFrom(new DateTime('2010-01-01'));

        (new Plenary())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($plenary)
        ;
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null]
            ],
            'Plenary' => [
                ['plenary_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                ['plenary_id' => 2, 'assembly_id' => 1, 'from' => '2000-01-01 00:00:00', 'to' => null, 'name' => null],
                [
                    'plenary_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'plenary_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
            ]
        ]);
    }
}
