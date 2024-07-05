<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class SessionTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->getPDO());

        $expectedData = (new Model\Session())
            ->setSessionId(1)
            ->setCongressmanId(1)
            ->setConstituencyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setType('þingmaður')
            ->setPartyId(1);
        $actualData = $sessionService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getNotFound()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->getPDO());

        $expectedData = null;
        $actualData = $sessionService->get(10000);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByCongressman()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Session())
                ->setSessionId(2)
                ->setCongressmanId(1)
                ->setConstituencyId(1)
                ->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-02'))
                ->setType('þingmaður')
                ->setPartyId(2),
            (new Model\Session())
                ->setSessionId(1)
                ->setCongressmanId(1)
                ->setConstituencyId(1)
                ->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-01'))
                ->setType('þingmaður')
                ->setPartyId(1)
        ];
        $actualData = $sessionService->fetchByCongressman(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByCongressmanNotFound()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->getPDO());

        $expectedData = [];
        $actualData = $sessionService->fetchByCongressman(10000);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByAssemblyAndCongressman()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Session())
                ->setSessionId(2)
                ->setCongressmanId(1)
                ->setConstituencyId(1)
                ->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-02'))
                ->setType('þingmaður')
                ->setPartyId(2),
            (new Model\Session())
                ->setSessionId(1)
                ->setCongressmanId(1)
                ->setConstituencyId(1)
                ->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-01'))
                ->setType('þingmaður')
                ->setPartyId(1)
        ];
        $actualData = $sessionService->fetchByAssemblyAndCongressman(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByAssemblyAndCongressmanNotFound()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->getPDO());

        $expectedData = [];
        $actualData = $sessionService->fetchByAssemblyAndCongressman(1, 1000);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getIdentifier()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->getPDO());

        $expectedData = 1;
        $actualData = $sessionService->getIdentifier(1, new \DateTime('2000-01-01'), 'þingmaður');

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getIdentifierNotFound()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->getPDO());

        $expectedData = null;
        $actualData = $sessionService->getIdentifier(10000, new \DateTime('2000-01-01'), 'þingmaður');

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        $session = (new Model\Session())
            ->setCongressmanId(1)
            ->setConstituencyId(1)
            ->setAssemblyId(2)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setType('þingmaður')
            ->setPartyId(1);

        $expectedTable = $this->createArrayDataSet([
            'Session' => [
                [
                    'session_id' => 1,
                    'congressman_id' => 1,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 1,
                    'from' => '2000-01-01',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 2,
                    'congressman_id' => 1,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-02',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 3,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-03',
                    'to' => null,
                    'type' => 'varamaður',
                    'abbr' => null
                ], [
                    'session_id' => 4,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-04',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 5,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-05',
                    'to' => null,
                    'type' => 'varamaður',
                    'abbr' => null
                ], [
                    'session_id' => 6,
                    'congressman_id' => 1,
                    'constituency_id' => 1,
                    'assembly_id' => 2,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ],
            ]
        ])->getTable('Session');
        $actualTable = $this->getConnection()->createQueryTable('Session', 'SELECT * FROM Session');

        $assemblyService = new Session();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->create($session);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createAlreadyExist()
    {
        $session = (new Model\Session())
            ->setCongressmanId(1)
            ->setConstituencyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setType('þingmaður')
            ->setPartyId(1);

        $assemblyService = new Session();
        $assemblyService->setDriver($this->getPDO());
        try {
            $assemblyService->create($session);
        } catch (\PDOException $e) {
            $this->assertEquals(1062, $e->errorInfo[1]);
        }
    }

    #[Test]
    public function updateSuccess()
    {
        $session = (new Model\Session())
            ->setSessionId(1)
            ->setCongressmanId(1)
            ->setConstituencyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setType('varamaður')
            ->setPartyId(1);

        $expectedTable = $this->createArrayDataSet([
            'Session' => [
                [
                    'session_id' => 1,
                    'congressman_id' => 1,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 1,
                    'from' => '2000-01-01',
                    'to' => null,
                    'type' => 'varamaður',
                    'abbr' => null
                ], [
                    'session_id' => 2,
                    'congressman_id' => 1,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-02',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 3,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-03',
                    'to' => null,
                    'type' => 'varamaður',
                    'abbr' => null
                ], [
                    'session_id' => 4,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-04',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 5,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-05',
                    'to' => null,
                    'type' => 'varamaður',
                    'abbr' => null
                ],
            ]
        ])->getTable('Session');
        $actualTable = $this->getConnection()->createQueryTable('Session', 'SELECT * FROM Session');

        $assemblyService = new Session();
        $assemblyService->setDriver($this->getPDO());
        $assemblyService->update($session);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function deleteSuccess()
    {
        $expectedTable = $this->createArrayDataSet([
            'Session' => [
                [
                    'session_id' => 2,
                    'congressman_id' => 1,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-02',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 3,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-03',
                    'to' => null,
                    'type' => 'varamaður',
                    'abbr' => null
                ], [
                    'session_id' => 4,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-04',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 5,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-05',
                    'to' => null,
                    'type' => 'varamaður',
                    'abbr' => null
                ],
            ]
        ])->getTable('Session');
        $actualTable = $this->getConnection()->createQueryTable('Session', 'SELECT * FROM Session');

        $assemblyService = new Session();
        $assemblyService->setDriver($this->getPDO());
        $affectedRowCound = $assemblyService->delete(1);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRowCound);
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

        $session = (new Model\Session())
            ->setCongressmanId(1)
            ->setConstituencyId(1)
            ->setAssemblyId(2)
            ->setFrom(new \DateTime('2021-01-01'))
            ->setType('þingmaður')
            ->setPartyId(1);

        (new Session())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->create($session);
    }

    #[Test]
    public function updateFireEventResourceFoundNoUpdatedNeeded()
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

        $session = (new Model\Session())
            ->setSessionId(1)
            ->setCongressmanId(1)
            ->setConstituencyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setType('þingmaður')
            ->setPartyId(1);

        (new Session())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($session);
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

        $session = (new Model\Session())
            ->setSessionId(1)
            ->setCongressmanId(1)
            ->setConstituencyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setTo(new \DateTime('2000-01-01'))
            ->setType('þingmaður')
            ->setPartyId(1);

        (new Session())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($session);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
            ],
            'Party' => [
                ['party_id' => 1, 'name' => 'p1', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 2, 'name' => 'p2', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 3, 'name' => 'p3', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name1', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 2, 'name' => 'name2', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name3', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 4, 'name' => 'name4', 'birth' => '2000-01-01', 'death' => null],
            ],
            'Constituency' => [
                ['constituency_id' => 1]
            ],
            'Session' => [
                [
                    'session_id' => 1,
                    'congressman_id' => 1,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 1,
                    'from' => '2000-01-01',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 2,
                    'congressman_id' => 1,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-02',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 3,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-03',
                    'to' => null,
                    'type' => 'varamaður',
                    'abbr' => null
                ], [
                    'session_id' => 4,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-04',
                    'to' => null,
                    'type' => 'þingmaður',
                    'abbr' => null
                ], [
                    'session_id' => 5,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'party_id' => 2,
                    'from' => '2000-01-05',
                    'to' => null,
                    'type' => 'varamaður',
                    'abbr' => null
                ],
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => Model\KindEnum::A->value],
            ],
            'ParliamentarySession' => [
                ['parliamentary_session_id' => 1, 'assembly_id' => 1],
            ],
        ]);
    }
}
