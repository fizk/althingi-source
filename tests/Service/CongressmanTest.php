<?php

namespace AlthingiTest\Service;

use Althingi\Service\Congressman;
use AlthingiTest\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\CongressmanAndCabinet;
use Althingi\Model\CongressmanAndParty;
use Althingi\Model\CongressmanAndDateRange;
use Althingi\Model\President;
use Althingi\Model\Proponent;
use Althingi\Model\Congressman as CongressmanModel;
use Althingi\Model\CongressmanValue as CongressmanValueModel;
use PDO;
use Mockery;
use Psr\EventDispatcher\EventDispatcherInterface;
use Althingi\Events\{UpdateEvent, AddEvent};

class CongressmanTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $expectedData = (new CongressmanModel())
            ->setCongressmanId(1)
            ->setName('name1')
            ->setBirth(new \DateTime('2000-01-01'));

        $actualData = $congressmanService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetNotFound()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $expectedData = null;

        $actualData = $congressmanService->get(10000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAll()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $this->assertCount(4, $congressmanService->fetchAll(0, 100));
    }

    public function testCount()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $this->assertEquals(4, $congressmanService->count());
    }

    public function testFetchByAssembly()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $this->assertCount(2, $congressmanService->fetchByAssembly(1));
        $this->assertCount(2, $congressmanService->fetchByAssembly(1, Congressman::CONGRESSMAN_TYPE_MP));
        $this->assertCount(1, $congressmanService->fetchByAssembly(1, Congressman::CONGRESSMAN_TYPE_SUBSTITUTE));
        $this->assertCount(0, $congressmanService->fetchByAssembly(1, Congressman::CONGRESSMAN_TYPE_WITH_SUBSTITUTE));
        $this->assertInstanceOf(CongressmanAndParty::class, ($congressmanService->fetchByAssembly(1))[0]);
    }

    public function testFetchByCabinet()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);
        $congressmen = $congressmanService->fetchByCabinet(1);

        $this->assertCount(1, $congressmen);
        $this->assertInstanceOf(CongressmanAndCabinet::class, $congressmen[0]);
    }

    public function testFetchPresidents()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $presidents = $congressmanService->fetchPresidents();

        $this->assertCount(3, $presidents);
        $this->assertInstanceOf(President::class, $presidents[0]);
    }

    public function testFetchProponents()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $proponents = $congressmanService->fetchProponents(1, 1);

        $this->assertCount(1, $proponents);
        $this->assertInstanceOf(Proponent::class, $proponents[0]);
    }

    public function testFetchProponentsByIssue()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $proponents = $congressmanService->fetchProponentsByIssue(1, 1);

        $this->assertCount(1, $proponents);
        $this->assertInstanceOf(Proponent::class, $proponents[0]);
    }

    public function testFetchAccumulatedTimeByIssue()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $issueTime = $congressmanService->fetchAccumulatedTimeByIssue(1, 1);

        $this->assertCount(1, $issueTime);
        $this->assertInstanceOf(CongressmanAndDateRange::class, $issueTime[0]);
    }

    public function testFetchPresidentsByAssembly()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $presidents = $congressmanService->fetchPresidentsByAssembly(1);

        $this->assertCount(1, $presidents);
        $this->assertInstanceOf(President::class, $presidents[0]);
    }

    public function testFetchTimeByAssembly()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo);

        $expectedData = [
            (new CongressmanValueModel())
                ->setCongressmanId(1)
                ->setName('name1')
                ->setBirth(new \DateTime('2000-01-01'))
                ->setValue(60),
            (new CongressmanValueModel())
                ->setCongressmanId(2)
                ->setName('name2')
                ->setBirth(new \DateTime('2000-01-01'))
                ->setValue(0),
        ];

        $actualData = $congressmanService->fetchTimeByAssembly(1, null);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof AddEvent;
            })
            ->getMock();

        $congressman = (new CongressmanModel())
            ->setName('name5')
            ->setBirth(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name1',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 2, 'name' => 'name2',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name3',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 4, 'name' => 'name4',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 5, 'name' => 'name5',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
            ],
        ])->getTable('Congressman');
        $actualTable = $this->getConnection()->createQueryTable('Congressman', 'SELECT * FROM Congressman');

        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $congressmanService->create($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof AddEvent;
            })
            ->getMock();

        $congressman = (new CongressmanModel())
            ->setName('name5')
            ->setBirth(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name1',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 2, 'name' => 'name2',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name3',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 4, 'name' => 'name4',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 5, 'name' => 'name5',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
            ],
        ])->getTable('Congressman');
        $actualTable = $this->getConnection()->createQueryTable('Congressman', 'SELECT * FROM Congressman');

        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $congressmanService->save($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
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

        $congressman = (new CongressmanModel())
            ->setCongressmanId(1)
            ->setName('hundur')
            ->setBirth(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'hundur',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 2, 'name' => 'name2',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name3',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 4, 'name' => 'name4',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
            ],
        ])->getTable('Congressman');
        $actualTable = $this->getConnection()->createQueryTable('Congressman', 'SELECT * FROM Congressman');

        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $congressmanService->update($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testDelete()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->never()
            // ->withArgs(function ($args) {
            //     return $args instanceof UpdateEvent;
            // })
            ->getMock();

        $expectedTable = $this->createArrayDataSet([
            'Congressman' => [
                ['congressman_id' => 2, 'name' => 'name2',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name3',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 4, 'name' => 'name4',
                    'abbreviation' => null, 'birth' => '2000-01-01', 'death' => null],
            ],
        ])->getTable('Congressman');
        $actualTable = $this->getConnection()->createQueryTable('Congressman', 'SELECT * FROM Congressman');

        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $congressmanService->delete(1);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Party' => [
                ['party_id' => 1, 'name' => 'party name', 'color' => '000000'],
            ],
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
            'President' => [
                [
                    'president_id' => 1,
                    'congressman_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01',
                    'title' => 'p1'
                ], [
                    'president_id' => 2,
                    'congressman_id' => 2,
                    'assembly_id' => 2,
                    'from' => '2000-01-01',
                    'title' => 'p2'
                ], [
                    'president_id' => 3,
                    'congressman_id' => 2,
                    'assembly_id' => 3,
                    'from' => '2000-01-01',
                    'title' => 'p3'
                ],
            ],
            'Constituency' => [
                ['constituency_id' => 1]
            ],
            'Cabinet' => [
                ['cabinet_id' => 1],
                ['cabinet_id' => 2],
                ['cabinet_id' => 3],
            ],
            'Cabinet_has_Congressman' => [
                ['cabinet_id' => 1, 'congressman_id' => 1, 'title' => 'some_title'],
                ['cabinet_id' => 2, 'congressman_id' => 2, 'title' => 'some_title'],
                ['cabinet_id' => 3, 'congressman_id' => 2, 'title' => 'some_title'],
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
                    'party_id' => 1
                ], [
                    'session_id' => 3,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-03',
                    'type' => 'varamaður',
                    'party_id' => 1
                ], [
                    'session_id' => 4,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-04',
                    'type' => 'þingmaður',
                    'party_id' => 1
                ], [
                    'session_id' => 5,
                    'congressman_id' => 2,
                    'constituency_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-05',
                    'type' => 'varamaður',
                    'party_id' => 1
                ],
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1, 'category' => 'A']
            ],
            'Document' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'category' => 'A',
                    'date' => '2000-01-01',
                    'url' => '',
                    'type' => ''
                ]
            ],
            'Document_has_Congressman' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'order' => 1
                ]
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
                    'from' => '2001-01-01 00:00:00',
                    'to' => '2001-01-01 00:01:00',
                ]
            ]
        ]);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
