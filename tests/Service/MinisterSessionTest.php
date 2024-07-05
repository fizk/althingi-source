<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class MinisterSessionTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $ministerSessionService = new MinisterSession();
        $ministerSessionService->setDriver($this->getPDO());

        $expectedData = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinisterSessionId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        $actualData = $ministerSessionService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllGeneratorAll()
    {
        $ministerSessionService = new MinisterSession();
        $ministerSessionService->setDriver($this->getPDO());

        $expectedData = [(new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinisterSessionId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))]
        ;

        $actualData = [];
        foreach ($ministerSessionService->fetchAllGenerator() as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllGeneratorByAssemblyFound()
    {
        $ministerSessionService = new MinisterSession();
        $ministerSessionService->setDriver($this->getPDO());

        $expectedData = [(new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinisterSessionId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))]
        ;

        $actualData = [];
        foreach ($ministerSessionService->fetchAllGenerator(1) as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllGeneratorByAssemblyNotFound()
    {
        $ministerSessionService = new MinisterSession();
        $ministerSessionService->setDriver($this->getPDO());

        $expectedData = []
        ;

        $actualData = [];
        foreach ($ministerSessionService->fetchAllGenerator(2) as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        $ministrySitting = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinisterSessionId(2)
            ->setMinistryId(2)
            ->setCongressmanId(2)
            ->setPartyId(2)
            ->setFrom(new \DateTime('2001-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'MinisterSession' => [
                [
                    'minister_session_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                [
                    'minister_session_id' => 2,
                    'assembly_id' => 1,
                    'ministry_id' => 2,
                    'congressman_id' => 2,
                    'party_id' => 2,
                    'from' => '2001-01-01',
                    'to' => null,
                ]
            ],
        ])->getTable('MinisterSession');
        $actualTable = $this->getConnection()->createQueryTable('MinisterSession', 'SELECT * FROM MinisterSession');

        $ministrySittingService = new MinisterSession();
        $ministrySittingService->setDriver($this->getPDO());
        $ministrySittingService->create($ministrySitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createAlreadyExist()
    {
        $ministrySitting = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinisterSessionId(2)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(2)
            ->setFrom(new \DateTime('2001-01-01'));

        $ministrySittingService = new MinisterSession();
        $ministrySittingService->setDriver($this->getPDO());
        try {
            $ministrySittingService->create($ministrySitting);
        } catch (\PDOException $e) {
            $this->assertEquals(1062, $e->errorInfo[1]);
        }
    }

    #[Test]
    public function saveUpdate()
    {
        $ministrySitting = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinisterSessionId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;

        $expectedTable = $this->createArrayDataSet([
            'MinisterSession' => [
                [
                    'minister_session_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ]
            ],
        ])->getTable('MinisterSession');
        $actualTable = $this->getConnection()->createQueryTable('MinisterSession', 'SELECT * FROM MinisterSession');

        $ministrySittingService = new MinisterSession();
        $ministrySittingService->setDriver($this->getPDO());
        $affectedRows = $ministrySittingService->save($ministrySitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(2, $affectedRows);
    }

    #[Test]
    public function saveCreate()
    {
        $ministrySitting = (new Model\MinisterSession())
            ->setAssemblyId(2)
            ->setMinisterSessionId(2)
            ->setMinistryId(2)
            ->setCongressmanId(2)
            ->setPartyId(2)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;

        $expectedTable = $this->createArrayDataSet([
            'MinisterSession' => [
                [
                    'minister_session_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                [
                    'minister_session_id' => 2,
                    'assembly_id' => 2,
                    'ministry_id' => 2,
                    'congressman_id' => 2,
                    'party_id' => 2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ]
            ],
        ])->getTable('MinisterSession');
        $actualTable = $this->getConnection()->createQueryTable('MinisterSession', 'SELECT * FROM MinisterSession');

        $ministrySittingService = new MinisterSession();
        $ministrySittingService->setDriver($this->getPDO());
        $affectedRows = $ministrySittingService->save($ministrySitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    #[Test]
    public function updateSuccess()
    {
        $ministrySitting = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinisterSessionId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;

        $expectedTable = $this->createArrayDataSet([
            'MinisterSession' => [
                [
                    'minister_session_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ]
            ],
        ])->getTable('MinisterSession');
        $actualTable = $this->getConnection()->createQueryTable('MinisterSession', 'SELECT * FROM MinisterSession');

        $ministrySittingService = new MinisterSession();
        $ministrySittingService->setDriver($this->getPDO());
        $ministrySittingService->update($ministrySitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function getIdentifier()
    {
        $ministerSessionService = new MinisterSession();
        $ministerSessionService->setDriver($this->getPDO());

        $expectedData = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        $actualData = $ministerSessionService->getIdentifier(
            $expectedData->getAssemblyId(),
            $expectedData->getMinistryId(),
            $expectedData->getCongressmanId(),
            $expectedData->getFrom()
        );

        $this->assertEquals(1, $actualData);
    }

    #[Test]
    public function getIdentifierNotFound()
    {
        $ministerSessionService = new MinisterSession();
        $ministerSessionService->setDriver($this->getPDO());

        $expectedData = (new Model\MinisterSession())
            ->setAssemblyId(100)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        $actualData = $ministerSessionService->getIdentifier(
            $expectedData->getAssemblyId(),
            $expectedData->getMinistryId(),
            $expectedData->getCongressmanId(),
            $expectedData->getFrom()
        );

        $this->assertEquals(false, $actualData);
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

        $ministrySitting = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinistryId(2)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        (new MinisterSession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($ministrySitting);
    }

    #[Test]
    public function updateFireEventResourceFoundNoUpdateRequired()
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

        $ministrySitting = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinisterSessionId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        (new MinisterSession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($ministrySitting);
    }

    #[Test]
    public function updateFireEventResourceFoundUpdateNeeded()
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

        $ministrySitting = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinisterSessionId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;

        (new MinisterSession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($ministrySitting);
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
                return true;
            })
            ->getMock();

        $ministrySitting = (new Model\MinisterSession())
            ->setAssemblyId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2022-01-01'))
        ;

        (new MinisterSession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($ministrySitting);
    }

    #[Test]
    public function saveFireEventResourceFoundNoNeedForUpdate()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
                return true;
            })
            ->getMock();

        $ministrySitting = (new Model\MinisterSession())
            ->setMinisterSessionId(1)
            ->setAssemblyId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
        ;

        (new MinisterSession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($ministrySitting);
    }

    #[Test]
    public function saveFireEventResourceFoundUpdateNeeded()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
                return true;
            })
            ->getMock();

        $ministrySitting = (new Model\MinisterSession())
            ->setMinisterSessionId(1)
            ->setAssemblyId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;

        (new MinisterSession())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($ministrySitting);
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
                ['congressman_id' => 2, 'name' => 'name1', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name1', 'birth' => '2000-01-01', 'death' => null],
            ],
            'Party' => [
                ['party_id' => 1, 'name' => 'p1', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 2, 'name' => 'p2', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 3, 'name' => 'p3', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
            ],
            'Ministry' => [
                [
                    'ministry_id' => 1,
                    'name' => 'name 1',
                    'abbr_short' => 'abbr_short1',
                    'abbr_long' => 'abbr_long1',
                    'first' => 1,
                    'last' => 3,
                ],
                [
                    'ministry_id' => 2,
                    'name' => 'name 2',
                    'abbr_short' => 'abbr_short2',
                    'abbr_long' => 'abbr_long2',
                    'first' => 1,
                    'last' => null,
                ],
            ],
            'MinisterSession' => [
                [
                    'minister_session_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ]
            ],
        ]);
    }
}
