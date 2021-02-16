<?php

namespace AlthingiTest\Service;

use Althingi\Service\Session;
use AlthingiTest\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Session as SessionModel;

class SessionTest extends TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->pdo);

        $expectedData = (new SessionModel())
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

    public function testGetNotFound()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->pdo);

        $expectedData = null;
        $actualData = $sessionService->get(10000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByCongressman()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->pdo);

        $expectedData = [
            (new SessionModel())
                ->setSessionId(2)
                ->setCongressmanId(1)
                ->setConstituencyId(1)
                ->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-02'))
                ->setType('þingmaður')
                ->setPartyId(2),
            (new SessionModel())
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

    public function testFetchByCongressmanNotFound()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->pdo);

        $expectedData = [];
        $actualData = $sessionService->fetchByCongressman(10000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByAssemblyAndCongressman()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->pdo);

        $expectedData = [
            (new SessionModel())
                ->setSessionId(2)
                ->setCongressmanId(1)
                ->setConstituencyId(1)
                ->setAssemblyId(1)
                ->setFrom(new \DateTime('2000-01-02'))
                ->setType('þingmaður')
                ->setPartyId(2),
            (new SessionModel())
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

    public function testFetchByAssemblyAndCongressmanNotFound()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->pdo);

        $expectedData = [];
        $actualData = $sessionService->fetchByAssemblyAndCongressman(1, 1000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetIdentifier()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->pdo);

        $expectedData = 1;
        $actualData = $sessionService->getIdentifier(1, new \DateTime('2000-01-01'), 'þingmaður');

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetIdentifierNotFound()
    {
        $sessionService = new Session();
        $sessionService->setDriver($this->pdo);

        $expectedData = null;
        $actualData = $sessionService->getIdentifier(10000, new \DateTime('2000-01-01'), 'þingmaður');

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $session = (new SessionModel())
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
        $assemblyService->setDriver($this->pdo);
        $assemblyService->create($session);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testCreateAlreadyExist()
    {
        $session = (new SessionModel())
            ->setCongressmanId(1)
            ->setConstituencyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01'))
            ->setType('þingmaður')
            ->setPartyId(1);

        $assemblyService = new Session();
        $assemblyService->setDriver($this->pdo);
        try {
            $assemblyService->create($session);
        } catch (\PDOException $e) {
            $this->assertEquals(1062, $e->errorInfo[1]);
        }
    }

    public function testUpdate()
    {
        $session = (new SessionModel())
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
        $assemblyService->setDriver($this->pdo);
        $assemblyService->update($session);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testDelete()
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
        $assemblyService->setDriver($this->pdo);
        $affectedRowCound = $assemblyService->delete(1);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRowCound);
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
                ['issue_id' => 1, 'assembly_id' => 1, 'category' => 'A'],
            ],
            'Plenary' => [
                ['plenary_id' => 1, 'assembly_id' => 1],
            ],
        ]);
    }
}
