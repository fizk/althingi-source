<?php

namespace Althingi\Service;

use PHPUnit\Framework\TestCase;
use Althingi\Model\Committee as CommitteeModel;
use Althingi\Service\Committee;
use Althingi\DatabaseConnection;
use Althingi\Events\{UpdateEvent, AddEvent};
use Mockery;
use PDO;
class CommitteeTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $expectedData = (new CommitteeModel())
            ->setFirstAssemblyId(1)
            ->setLastAssemblyId(2)
            ->setCommitteeId(1)
            ->setAbbrShort('c1')
            ->setAbbrLong('com1')
            ->setName('committee1');

        $service = new Committee();
        $service->setDriver($this->pdo);

        $this->assertEquals($expectedData, $service->get(1));
    }

    public function testGetNotFound()
    {
        $service = new Committee();
        $service->setDriver($this->pdo);

        $this->assertNull($service->get(100));
    }

    public function testFetchAll()
    {
        $service = new Committee();
        $service->setDriver($this->pdo);

        $this->assertIsArray($service->fetchAll());
        $this->assertCount(3, $service->fetchAll());
    }

    public function testFetchByAssembly()
    {
        $service = new Committee();
        $service->setDriver($this->pdo);

        $service->fetchByAssembly(1);

        $this->assertCount(3, $service->fetchByAssembly(1));
    }

    public function testCreate()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->times(1)
            ->getMock();

        $expectedTable = $this->createArrayDataSet([
            'Committee' => [
                [
                    'committee_id' => 1,
                    'name' => 'committee1',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com1',
                    'abbr_short' => 'c1'
                ], [
                    'committee_id' => 2,
                    'name' => 'committee2',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com2',
                    'abbr_short' => 'c2'
                ], [
                    'committee_id' => 3,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ], [
                    'committee_id' => 4,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
            ],
        ])->getTable('Committee');
        $actualTable = $this->getConnection()->createQueryTable('Committee', 'SELECT * FROM Committee');

        $committee = (new CommitteeModel())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(4);

        (new Committee())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->create($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testCreateNegative()
    {
        $expectedTable = $this->createArrayDataSet([
            'Committee' => [
                 [
                    'committee_id' => -4,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
                [
                    'committee_id' => 1,
                    'name' => 'committee1',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com1',
                    'abbr_short' => 'c1'
                ], [
                    'committee_id' => 2,
                    'name' => 'committee2',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com2',
                    'abbr_short' => 'c2'
                ], [
                    'committee_id' => 3,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
            ],
        ])->getTable('Committee');
        $actualTable = $this->getConnection()->createQueryTable('Committee', 'SELECT * FROM Committee');

        $committee = (new CommitteeModel())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(-4);

        $service = new Committee();
        $service->setDriver($this->pdo);
        $service->create($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof AddEvent;
            }))
            ->times(1)
            ->getMock();

        $expectedTable = $this->createArrayDataSet([
            'Committee' => [
                [
                    'committee_id' => 1,
                    'name' => 'committee1',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com1',
                    'abbr_short' => 'c1'
                ], [
                    'committee_id' => 2,
                    'name' => 'committee2',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com2',
                    'abbr_short' => 'c2'
                ], [
                    'committee_id' => 3,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ], [
                    'committee_id' => 4,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
            ],
        ])->getTable('Committee');
        $actualTable = $this->getConnection()->createQueryTable('Committee', 'SELECT * FROM Committee');

        $committee = (new CommitteeModel())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(4);

        (new Committee())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->pdo)
            ->save($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSaveNegative()
    {
        $expectedTable = $this->createArrayDataSet([
            'Committee' => [
                 [
                    'committee_id' => -4,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
                [
                    'committee_id' => 1,
                    'name' => 'committee1',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com1',
                    'abbr_short' => 'c1'
                ], [
                    'committee_id' => 2,
                    'name' => 'committee2',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com2',
                    'abbr_short' => 'c2'
                ], [
                    'committee_id' => 3,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
            ],
        ])->getTable('Committee');
        $actualTable = $this->getConnection()->createQueryTable('Committee', 'SELECT * FROM Committee');

        $committee = (new CommitteeModel())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(-4);

        $service = new Committee();
        $service->setDriver($this->pdo);
        $service->save($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSaveZeroId()
    {
        $expectedTable = $this->createArrayDataSet([
            'Committee' => [
                [
                    'committee_id' => 0,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
                [
                    'committee_id' => 1,
                    'name' => 'committee1',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com1',
                    'abbr_short' => 'c1'
                ], [
                    'committee_id' => 2,
                    'name' => 'committee2',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com2',
                    'abbr_short' => 'c2'
                ], [
                    'committee_id' => 3,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
            ],
        ])->getTable('Committee');
        $actualTable = $this->getConnection()->createQueryTable('Committee', 'SELECT * FROM Committee');

        $committee = (new CommitteeModel())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(0);

        $service = new Committee();
        $service->setDriver($this->pdo);
        $service->save($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof UpdateEvent;
            }))
            ->times(1)
            ->getMock();

        $expectedTable = $this->createArrayDataSet([
            'Committee' => [
                [
                    'committee_id' => 1,
                    'name' => 'thisIsTheNewName',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com1',
                    'abbr_short' => 'c1'
                ], [
                    'committee_id' => 2,
                    'name' => 'committee2',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com2',
                    'abbr_short' => 'c2'
                ], [
                    'committee_id' => 3,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
            ],
        ])->getTable('Committee');
        $actualTable = $this->getConnection()->createQueryTable('Committee', 'SELECT * FROM Committee');

        $committee = (new CommitteeModel())
            ->setCommitteeId(1)
            ->setName('thisIsTheNewName')
            ->setFirstAssemblyId(1)
            ->setLastAssemblyId(2)
            ->setAbbrLong('com1')
            ->setAbbrShort('c1');

        $service = (new Committee())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 4, 'from' => '2000-01-01', 'to' => null],
            ],
            'Committee' => [
                [
                    'committee_id' => 1,
                    'name' => 'committee1',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com1',
                    'abbr_short' => 'c1'
                ], [
                    'committee_id' => 2,
                    'name' => 'committee2',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'com2',
                    'abbr_short' => 'c2'
                ], [
                    'committee_id' => 3,
                    'name' => null,
                    'first_assembly_id' => 1,
                    'last_assembly_id' => null,
                    'abbr_long' => null,
                    'abbr_short' => null
                ],
            ]
        ]);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
