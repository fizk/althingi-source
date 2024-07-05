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

class ParliamentarySessionTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $parliamentarySessionService = new ParliamentarySession();
        $parliamentarySessionService->setDriver($this->getPDO());

        $expectedData = (new Model\ParliamentarySession())
            ->setParliamentarySessionId(1)
            ->setFrom(new \DateTime('2000-01-01 00:00:00'))
            ->setAssemblyId(1);

        $actualData = $parliamentarySessionService->get(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getNotFound()
    {
        $parliamentarySessionService = new ParliamentarySession();
        $parliamentarySessionService->setDriver($this->getPDO());

        $expectedData = null;

        $actualData = $parliamentarySessionService->get(1, 100);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByAssembly()
    {
        $parliamentarySessionService = new ParliamentarySession();
        $parliamentarySessionService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\ParliamentarySession())->setParliamentarySessionId(1)->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-01')),
            (new Model\ParliamentarySession())->setParliamentarySessionId(2)->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-01')),
            (new Model\ParliamentarySession())->setParliamentarySessionId(3)
                ->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01'))
                ->setTo(new \DateTime('2001-01-01')),
            (new Model\ParliamentarySession())->setParliamentarySessionId(4)
                ->setAssemblyId(1)->setFrom(new \DateTime('2000-01-01'))
                ->setTo(new \DateTime('2001-01-01'))->setName('p-name'),
        ];

        $actualData = $parliamentarySessionService->fetchByAssembly(1, 0, 20);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByAssemblyNotFound()
    {
        $parliamentarySessionService = new ParliamentarySession();
        $parliamentarySessionService->setDriver($this->getPDO());

        $expectedData = [];

        $actualData = $parliamentarySessionService->fetchByAssembly(100, 0, 20);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function countByAssembly()
    {
        $parliamentarySessionService = new ParliamentarySession();
        $parliamentarySessionService->setDriver($this->getPDO());

        $expectedData = 4;
        $actualData = $parliamentarySessionService->countByAssembly(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function countByAssemblyNotFound()
    {
        $parliamentarySessionService = new ParliamentarySession();
        $parliamentarySessionService->setDriver($this->getPDO());

        $expectedData = 0;
        $actualData = $parliamentarySessionService->countByAssembly(100);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(5);

        $assemblyService = new ParliamentarySession();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->create($parliamentarySession);

        $expectedTable = $this->createArrayDataSet([
            'ParliamentarySession' => [
                [
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => null
                ],[
                    'parliamentary_session_id' => 2,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => null
                ],[
                    'parliamentary_session_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'parliamentary_session_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
                [
                    'parliamentary_session_id' => 5,
                    'assembly_id' => 1,
                    'from' => null,
                    'to' => null,
                    'name' => null
                ],
            ],
        ])->getTable('ParliamentarySession');
        $queryTable = $this->getConnection()->createQueryTable(
            'ParliamentarySession',
            'SELECT * FROM ParliamentarySession'
        );

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function createNegative()
    {
        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(-5);

        $assemblyService = new ParliamentarySession();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->create($parliamentarySession);

        $expectedTable = $this->createArrayDataSet([
            'ParliamentarySession' => [
                [
                    'parliamentary_session_id' => -5,
                    'assembly_id' => 1,
                    'from' => null,
                    'to' => null,
                    'name' => null
                ],[
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null, 'name' => null
                ],[
                    'parliamentary_session_id' => 2,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null, 'name' => null
                ],[
                    'parliamentary_session_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'parliamentary_session_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
            ],
        ])->getTable('ParliamentarySession');
        $queryTable = $this->getConnection()->createQueryTable(
            'ParliamentarySession',
            'SELECT * FROM ParliamentarySession'
        );

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(5);

        $assemblyService = new ParliamentarySession();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->save($parliamentarySession);

        $expectedTable = $this->createArrayDataSet([
            'ParliamentarySession' => [
                [
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => null
                ],[
                    'parliamentary_session_id' => 2,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => null
                ],[
                    'parliamentary_session_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'parliamentary_session_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ], [
                    'parliamentary_session_id' => 5,
                    'assembly_id' => 1,
                    'from' => null,
                    'to' => null,
                    'name' => null
                ],
            ],
        ])->getTable('ParliamentarySession');
        $queryTable = $this->getConnection()->createQueryTable(
            'ParliamentarySession',
            'SELECT * FROM ParliamentarySession'
        );

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function saveNegative()
    {
        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(-5);

        $assemblyService = new ParliamentarySession();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->save($parliamentarySession);

        $expectedTable = $this->createArrayDataSet([
            'ParliamentarySession' => [
                [
                    'parliamentary_session_id' => -5,
                    'assembly_id' => 1,
                    'from' => null,
                    'to' => null,
                    'name' => null
                ],[
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null, 'name' => null
                ],[
                    'parliamentary_session_id' => 2,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null, 'name' => null
                ],[
                    'parliamentary_session_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'parliamentary_session_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
            ],
        ])->getTable('ParliamentarySession');
        $queryTable = $this->getConnection()->createQueryTable(
            'ParliamentarySession',
            'SELECT * FROM ParliamentarySession'
        );

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    #[Test]
    public function updateSuccess()
    {
        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(1)
            ->setFrom(new \DateTime('2000-01-01 00:00:00'))
            ->setName('NewName');

        $assemblyService = new ParliamentarySession();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->update($parliamentarySession);

        $expectedTable = $this->createArrayDataSet([
            'ParliamentarySession' => [
                [
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => 'NewName'
                ], [
                    'parliamentary_session_id' => 2,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => null
                ], [
                    'parliamentary_session_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'parliamentary_session_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
            ],
        ])->getTable('ParliamentarySession');
        $queryTable = $this->getConnection()->createQueryTable(
            'ParliamentarySession',
            'SELECT * FROM ParliamentarySession'
        );

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

        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(5);

        (new ParliamentarySession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->create($parliamentarySession)
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

        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(1)
            ->setFrom(new DateTime('2000-01-01'));

        (new ParliamentarySession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($parliamentarySession)
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

        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(1)
            ->setFrom(new DateTime('2001-01-01'));

        (new ParliamentarySession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($parliamentarySession)
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

        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(10)
            ->setFrom(new DateTime('2001-01-01'));

        (new ParliamentarySession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($parliamentarySession)
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

        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(1)
            ->setFrom(new DateTime('2000-01-01'));

        (new ParliamentarySession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($parliamentarySession)
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

        $parliamentarySession = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(1)
            ->setFrom(new DateTime('2010-01-01'));

        (new ParliamentarySession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($parliamentarySession)
        ;
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null]
            ],
            'ParliamentarySession' => [
                [
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => null
                ],[
                    'parliamentary_session_id' => 2,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'name' => null
                ],[
                    'parliamentary_session_id' => 3,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => null
                ], [
                    'parliamentary_session_id' => 4,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2001-01-01 00:00:00',
                    'name' => 'p-name'
                ],
            ]
        ]);
    }
}
