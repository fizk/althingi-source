<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class PartyTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $partyService = new Party();
        $partyService->setDriver($this->getPDO());

        $expectedData = (new Model\Party())
            ->setPartyId(1)
            ->setName('p1')
            ->setColor('ffffff');

        $actualData = $partyService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getByCongressman()
    {
        $service = new Party();
        $service->setDriver($this->getPDO());

        $expectedParty = (new Model\Party())
            ->setPartyId(1)
            ->setName('p1')
            ->setColor('ffffff');

        $actualParty = $service->getByCongressman(1, new \DateTime('2002-01-02'));

        $this->assertEquals($expectedParty, $actualParty);
    }

    #[Test]
    public function fetchByAssembly()
    {
        $partyService = new Party();
        $partyService->setDriver($this->getPDO());

        $this->assertCount(2, $partyService->fetchByAssembly(1));
        $this->assertCount(1, $partyService->fetchByAssembly(1, [1]));
        $this->assertInstanceOf(Model\Party::class, ($partyService->fetchByAssembly(1))[0]);
    }

    #[Test]
    public function fetchElectedByAssembly()
    {
        $partyService = new Party();
        $partyService->setDriver($this->getPDO());
        $expectedParties = [
            (new \Althingi\Model\PartyAndElection())
                ->setResults(99.0)
                ->setElectionId(1)
                ->setElectionResultId(1)
                ->setAssemblyId(1)
                ->setPartyId(1)
                ->setName('p1')
                ->setColor('ffffff'),
            (new \Althingi\Model\PartyAndElection())
                ->setResults(98.0)
                ->setElectionId(1)
                ->setElectionResultId(2)
                ->setAssemblyId(1)
                ->setPartyId(2)
                ->setName('p2')
                ->setColor('ffffff'),
        ];
        $actualParties = $partyService->fetchElectedByAssembly(1);

        $this->assertEquals($expectedParties, $actualParties);
    }

    #[Test]
    public function fetchByCongressman()
    {
        $partyService = new Party();
        $partyService->setDriver($this->getPDO());
        $expectedParties = [
            (new Model\Party())->setPartyId(1)->setName('p1')->setColor('ffffff'),
            (new Model\Party())->setPartyId(2)->setName('p2')->setColor('ffffff'),
        ];
        $actualParties = $partyService->fetchByCongressman(1);

        $this->assertEquals($expectedParties, $actualParties);
    }

    #[Test]
    public function fetchByCabinet()
    {
        $partyService = new Party();
        $partyService->setDriver($this->getPDO());

        $expectedParties = [(new Model\Party())
            ->setPartyId(1)
            ->setName('p1')
            ->setColor('ffffff')];

        $actualParties = $partyService->fetchByCabinet(1);

        $this->assertEquals($expectedParties, $actualParties);
    }

    #[Test]
    public function fetchTimeByAssembly()
    {
        $partyService = new Party();
        $partyService->setDriver($this->getPDO());
        $expectedParties = [
            (new Model\PartyAndTime())->setPartyId(2)->setName('p2')->setColor('ffffff')->setTotalTime(600),
            (new Model\PartyAndTime())->setPartyId(1)->setName('p1')->setColor('ffffff')->setTotalTime(600),
        ];
        $actualParties = $partyService->fetchTimeByAssembly(1);

        $this->assertEquals($expectedParties, $actualParties);
    }

    #[Test]
    public function createSuccess()
    {
        $party = (new Model\Party())
            ->setPartyId(4)
            ->setName('p4')
            ->setColor('000000');

        $expectedTable = $this->createArrayDataSet([
            'Party' => [
                ['party_id' => 1, 'name' => 'p1', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 2, 'name' => 'p2', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 3, 'name' => 'p3', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 4, 'name' => 'p4', 'abbr_short' => null, 'abbr_long' => null, 'color' => '000000'],
            ],
        ])->getTable('Party');
        $actualTable = $this->getConnection()->createQueryTable('Party', 'SELECT * FROM Party');

        $partyService = new Party();
        $partyService->setDriver($this->getPDO());
        $partyService->create($party);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $party = (new Model\Party())
            ->setName('p4')
            ->setPartyId(4)
            ->setColor('000000');

        $expectedTable = $this->createArrayDataSet([
            'Party' => [
                ['party_id' => 1, 'name' => 'p1', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 2, 'name' => 'p2', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 3, 'name' => 'p3', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 4, 'name' => 'p4', 'abbr_short' => null, 'abbr_long' => null, 'color' => '000000'],
            ],
        ])->getTable('Party');
        $actualTable = $this->getConnection()->createQueryTable('Party', 'SELECT * FROM Party');

        $partyService = new Party();
        $partyService->setDriver($this->getPDO());
        $partyService->save($party);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function updateSuccess()
    {
        $party = (new Model\Party())
            ->setPartyId(1)
            ->setName('p1')
            ->setColor('000000');

        $expectedTable = $this->createArrayDataSet([
            'Party' => [
                ['party_id' => 1, 'name' => 'p1', 'abbr_short' => null, 'abbr_long' => null, 'color' => '000000'],
                ['party_id' => 2, 'name' => 'p2', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 3, 'name' => 'p3', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
            ],
        ])->getTable('Party');
        $actualTable = $this->getConnection()->createQueryTable('Party', 'SELECT * FROM Party');

        $partyService = new Party();
        $partyService->setDriver($this->getPDO());
        $partyService->update($party);

        $this->assertTablesEqual($expectedTable, $actualTable);
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

        $party = (new Model\Party())
            ->setPartyId(4)
            ->setName('p4')
            ->setColor('000000');

        (new Party())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->create($party);
    }

    #[Test]
    public function updateFireEventResourceFoundNoUpdatedRequired()
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

        $party = (new Model\Party())
            ->setPartyId(3)
            ->setName('p3')
            ->setColor('ffffff');

        (new Party())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($party);
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

        $party = (new Model\Party())
            ->setPartyId(3)
            ->setName('p3')
            ->setColor('000000');

        (new Party())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->update($party);
    }

    #[Test]
    public function saveFireEventResourceCreate()
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

        $party = (new Model\Party())
            ->setPartyId(4)
            ->setName('p4')
            ->setColor('000000');

        (new Party())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($party);
    }

    #[Test]
    public function saveFireEventResourceFoundNoUpdateNeeded()
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

        $party = (new Model\Party())
            ->setPartyId(3)
            ->setName('p3')
            ->setColor('ffffff');

        (new Party())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($party);
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
            })
            ->getMock();

        $party = (new Model\Party())
            ->setPartyId(3)
            ->setName('p3')
            ->setColor('123456');

        (new Party())
            ->setDriver($this->getPDO())
            ->setEventDispatcher(($eventDispatcher))
            ->save($party);
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
                    'from' => '2000-01-01',
                    'type' => 'þingmaður',
                    'party_id' => 1
                ], [
                    'session_id' => 2,
                    'congressman_id' => 1,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-02',
                    'type' => 'þingmaður',
                    'party_id' => 2
                ], [
                    'session_id' => 3,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-03',
                    'type' => 'varamaður',
                    'party_id' => 2
                ], [
                    'session_id' => 4,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-04',
                    'type' => 'þingmaður',
                    'party_id' => 2
                ], [
                    'session_id' => 5,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-05',
                    'type' => 'varamaður',
                    'party_id' => 2
                ],
            ],
            'Election' => [
                ['election_id' => 1, 'date' => '2000-01-01'],
                ['election_id' => 2, 'date' => '2004-01-01'],
            ],
            'ElectionResult' => [
                ['election_result_id' => 1, 'election_id' => 1, 'party_id' => 1, 'result' => 99.00],
                ['election_result_id' => 2, 'election_id' => 1, 'party_id' => 2, 'result' => 98.00],
                ['election_result_id' => 3, 'election_id' => 2, 'party_id' => 2, 'result' => 97.00],
                ['election_result_id' => 4, 'election_id' => 2, 'party_id' => 3, 'result' => 98.00],
            ],
            'Election_has_Assembly' => [
                ['election_id' => 1, 'assembly_id' => 1],
                ['election_id' => 2, 'assembly_id' => 2],
            ],
            'Cabinet' => [
                ['cabinet_id' => 1]
            ],
            'Cabinet_has_Congressman' => [
                ['cabinet_id' => 1, 'congressman_id' => 1, 'title' => 'dude', 'from' => '2000-01-01']
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => Model\KindEnum::A->value],
            ],
            'Plenary' => [
                ['plenary_id' => 1, 'assembly_id' => 1],
            ],
            'Speech' => [
                [
                    'speech_id' => 1,
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'from' => '2001-01-01 01:00:00',
                    'to' => '2001-01-01 01:10:00'
                ]
            ],

        ]);
    }
}
