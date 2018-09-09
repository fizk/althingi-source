<?php

namespace AlthingiTest\Service;

use Althingi\Service\Party;
use Althingi\ServiceEvents\ServiceEventsListener;
use AlthingiTest\DatabaseConnection;
use AlthingiTest\ElasticBlackHoleClient;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Party as PartyModel;
use Althingi\Model\PartyAndTime as PartyAndTimeModel;
use Psr\Log\NullLogger;
use Zend\EventManager\EventManager;

class PartyTest extends TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $partyService = new Party();
        $partyService->setDriver($this->pdo);

        $expectedData = (new PartyModel())
            ->setPartyId(1)
            ->setName('p1')
            ->setColor('ffffff');

        $actualData = $partyService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetByCongressman()
    {
        $service = new Party();
        $service->setDriver($this->pdo);

        $expectedParty = (new PartyModel())
            ->setPartyId(1)
            ->setName('p1')
            ->setColor('ffffff');

        $actualParty = $service->getByCongressman(1, new \DateTime('2002-01-02'));

        $this->assertEquals($expectedParty, $actualParty);
    }

    public function testFetchByAssembly()
    {
        $partyService = new Party();
        $partyService->setDriver($this->pdo);

        $this->assertCount(2, $partyService->fetchByAssembly(1));
        $this->assertCount(1, $partyService->fetchByAssembly(1, [1]));
        $this->assertInstanceOf(PartyModel::class, ($partyService->fetchByAssembly(1))[0]);
    }

    public function testFetchElectedByAssembly()
    {
        $partyService = new Party();
        $partyService->setDriver($this->pdo);
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

    public function testFetchByCongressman()
    {
        $partyService = new Party();
        $partyService->setDriver($this->pdo);
        $expectedParties = [
            (new PartyModel())->setPartyId(1)->setName('p1')->setColor('ffffff'),
            (new PartyModel())->setPartyId(2)->setName('p2')->setColor('ffffff'),
        ];
        $actualParties = $partyService->fetchByCongressman(1);

        $this->assertEquals($expectedParties, $actualParties);
    }

    public function testFetchByCabinet()
    {
        $partyService = new Party();
        $partyService->setDriver($this->pdo);

        $expectedParties = [(new PartyModel())
            ->setPartyId(1)
            ->setName('p1')
            ->setColor('ffffff')];

        $actualParties = $partyService->fetchByCabinet(1);

        $this->assertEquals($expectedParties, $actualParties);
    }

    public function testFetchTimeByAssembly()
    {
        $partyService = new Party();
        $partyService->setDriver($this->pdo);
        $expectedParties = [
            (new PartyAndTimeModel())->setPartyId(2)->setName('p2')->setColor('ffffff')->setTotalTime(600),
            (new PartyAndTimeModel())->setPartyId(1)->setName('p1')->setColor('ffffff')->setTotalTime(600),
        ];
        $actualParties = $partyService->fetchTimeByAssembly(1);

        $this->assertEquals($expectedParties, $actualParties);
    }

    public function testCreate()
    {
        $serviceEventListener = (new ServiceEventsListener())
            ->setElasticSearchClient(new ElasticBlackHoleClient())
            ->setLogger(new NullLogger());
        $eventManager = new EventManager();
        $serviceEventListener->attach($eventManager);

        $party = (new PartyModel())
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
        $partyService->setDriver($this->pdo);
        $partyService->setEventManager($eventManager);
        $partyService->create($party);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $serviceEventListener = (new ServiceEventsListener())
            ->setElasticSearchClient(new ElasticBlackHoleClient())
            ->setLogger(new NullLogger());
        $eventManager = new EventManager();
        $serviceEventListener->attach($eventManager);

        $party = (new PartyModel())
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
        $partyService->setDriver($this->pdo);
        $partyService->setEventManager($eventManager);
        $partyService->save($party);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $serviceEventListener = (new ServiceEventsListener())
            ->setElasticSearchClient(new ElasticBlackHoleClient())
            ->setLogger(new NullLogger());
        $eventManager = new EventManager();
        $serviceEventListener->attach($eventManager);

        $party = (new PartyModel())
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
        $partyService->setDriver($this->pdo);
        $partyService->setEventManager($eventManager);
        $partyService->update($party);

        $this->assertTablesEqual($expectedTable, $actualTable);
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
                ['session_id' => 1, 'congressman_id' => 1, 'constituency_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01', 'type' => 'þingmaður', 'party_id' => 1],
                ['session_id' => 2, 'congressman_id' => 1, 'constituency_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-02', 'type' => 'þingmaður', 'party_id' => 2],
                ['session_id' => 3, 'congressman_id' => 2, 'constituency_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-03', 'type' => 'varamaður', 'party_id' => 2],
                ['session_id' => 4, 'congressman_id' => 2, 'constituency_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-04', 'type' => 'þingmaður', 'party_id' => 2],
                ['session_id' => 5, 'congressman_id' => 2, 'constituency_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-05', 'type' => 'varamaður', 'party_id' => 2],
            ],
            'Election' => [
                ['election_id' => 1, 'date'=> '2000-01-01'],
                ['election_id' => 2, 'date'=> '2004-01-01'],
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
                ['issue_id' => 1, 'assembly_id' => 1, 'category' => 'A'],
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
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => '2001-01-01 01:00:00',
                    'to' => '2001-01-01 01:10:00'
                ]
            ],

        ]);
    }
}
