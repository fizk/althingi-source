<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test, After};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class CongressmanTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[After]
    public function down(): void
    {
        Mockery::close();
    }

    #[Test]
    public function getSuccess()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $expectedData = (new Model\Congressman())
            ->setCongressmanId(1)
            ->setName('name1')
            ->setBirth(new \DateTime('2000-01-01'));

        $actualData = $congressmanService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getNotFound()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $expectedData = null;

        $actualData = $congressmanService->get(10000);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAll()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $this->assertCount(4, $congressmanService->fetchAll(0, 100));
    }

    #[Test]
    public function countSuccess()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $this->assertEquals(4, $congressmanService->count());
    }

    #[Test]
    public function fetchByAssembly()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $this->assertCount(2, $congressmanService->fetchByAssembly(1));
        $this->assertCount(2, $congressmanService->fetchByAssembly(1, Congressman::CONGRESSMAN_TYPE_MP));
        $this->assertCount(1, $congressmanService->fetchByAssembly(1, Congressman::CONGRESSMAN_TYPE_SUBSTITUTE));
        $this->assertCount(0, $congressmanService->fetchByAssembly(1, Congressman::CONGRESSMAN_TYPE_WITH_SUBSTITUTE));
        $this->assertInstanceOf(Model\CongressmanAndParty::class, ($congressmanService->fetchByAssembly(1))[0]);
    }

    #[Test]
    public function fetchByCabinet()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());
        $congressmen = $congressmanService->fetchByCabinet(1);

        $this->assertCount(1, $congressmen);
        $this->assertInstanceOf(Model\CongressmanAndCabinet::class, $congressmen[0]);
    }

    #[Test]
    public function fetchPresidents()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $presidents = $congressmanService->fetchPresidents();

        $this->assertCount(3, $presidents);
        $this->assertInstanceOf(Model\President::class, $presidents[0]);
    }

    #[Test]
    public function fetchProponents()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $proponents = $congressmanService->fetchProponents(1, 1);

        $this->assertCount(1, $proponents);
        $this->assertInstanceOf(Model\Proponent::class, $proponents[0]);
    }

    #[Test]
    public function fetchProponentsByIssue()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $proponents = $congressmanService->fetchProponentsByIssue(1, 1);

        $this->assertCount(1, $proponents);
        $this->assertInstanceOf(Model\Proponent::class, $proponents[0]);
    }

    #[Test]
    public function fetchAccumulatedTimeByIssue()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $issueTime = $congressmanService->fetchAccumulatedTimeByIssue(1, 1);

        $this->assertCount(1, $issueTime);
        $this->assertInstanceOf(Model\CongressmanAndDateRange::class, $issueTime[0]);
    }

    #[Test]
    public function fetchPresidentsByAssembly()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $presidents = $congressmanService->fetchPresidentsByAssembly(1);

        $this->assertCount(1, $presidents);
        $this->assertInstanceOf(Model\President::class, $presidents[0]);
    }

    #[Test]
    public function fetchTimeByAssembly()
    {
        $congressmanService = new Congressman();
        $congressmanService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\CongressmanValue())
                ->setCongressmanId(1)
                ->setName('name1')
                ->setBirth(new \DateTime('2000-01-01'))
                ->setValue(60),
            (new Model\CongressmanValue())
                ->setCongressmanId(2)
                ->setName('name2')
                ->setBirth(new \DateTime('2000-01-01'))
                ->setValue(0),
        ];

        $actualData = $congressmanService->fetchTimeByAssembly(1, null);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof AddEvent;
            })
            ->getMock();

        $congressman = (new Model\Congressman())
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
        $congressmanService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $congressmanService->create($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveSuccess()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof AddEvent;
            })
            ->getMock();

        $congressman = (new Model\Congressman())
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
        $congressmanService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $congressmanService->save($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function updateSuccess()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $congressman = (new Model\Congressman())
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
        $congressmanService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $congressmanService->update($congressman);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function deleteSuccess()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
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
        $congressmanService->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $congressmanService->delete(1);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createFireEventOneEntryCreated()
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

        $congressman = (new Model\Congressman())
            ->setName('name5')
            ->setBirth(new \DateTime('2000-01-01'));

        (new Congressman())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($congressman);
    }

    #[Test]
    public function updateFireEventZeroNoUpdate()
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

        $congressman = (new Model\Congressman())
            ->setCongressmanId(1)
            ->setName('name1')
            ->setBirth(new \DateTime('2000-01-01'));

        (new Congressman())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($congressman);
    }

    #[Test]
    public function updateFireEventOneDidAnUpdate()
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

        $congressman = (new Model\Congressman())
            ->setCongressmanId(1)
            ->setName('name1-update')
            ->setBirth(new \DateTime('2000-01-01'));

        (new Congressman())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($congressman);
    }

    #[Test]
    public function saveFireEventZeroFoundAnEntryButNoUpdatedRequired()
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

        $congressman = (new Model\Congressman())
            ->setCongressmanId(1)
            ->setName('name1')
            ->setBirth(new \DateTime('2000-01-01'));

        (new Congressman())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($congressman);
    }

    #[Test]
    public function saveFireEventOneCreatedNewEntry()
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

        $congressman = (new Model\Congressman())
            ->setCongressmanId(5)
            ->setName('name1')
            ->setBirth(new \DateTime('2000-01-01'));

        (new Congressman())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($congressman);
    }

    #[Test]
    public function saveFireEventTwoFoundEntryAndUpdatedIt()
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

        $congressman = (new Model\Congressman())
            ->setCongressmanId(1)
            ->setName('name1-update')
            ->setBirth(new \DateTime('2000-01-01'));

        (new Congressman())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($congressman);
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
                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => Model\KindEnum::A->value]
            ],
            'Document' => [
                [
                    'document_id' => 1,
                    'issue_id' => 1,
                    'assembly_id' => 1,
                    'kind' => Model\KindEnum::A->value,
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
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'order' => 1
                ]
            ],
            'ParliamentarySession' => [
                ['parliamentary_session_id' => 1, 'assembly_id' => 1],
            ],
            'Speech' => [
                [
                    'speech_id' => 1,
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'from' => '2001-01-01 00:00:00',
                    'to' => '2001-01-01 00:01:00',
                ]
            ]
        ]);
    }
}
