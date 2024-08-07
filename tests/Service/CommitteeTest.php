<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test, After};
use PHPUnit\Framework\TestCase;

class CommitteeTest extends TestCase
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
        $expectedData = (new Model\Committee())
            ->setFirstAssemblyId(1)
            ->setLastAssemblyId(2)
            ->setCommitteeId(1)
            ->setAbbrShort('c1')
            ->setAbbrLong('com1')
            ->setName('committee1');

        $service = new Committee();
        $service->setDriver($this->getPDO());

        $this->assertEquals($expectedData, $service->get(1));
    }

    #[Test]
    public function getNotFound()
    {
        $service = new Committee();
        $service->setDriver($this->getPDO());

        $this->assertNull($service->get(100));
    }

    #[Test]
    public function fetchAll()
    {
        $service = new Committee();
        $service->setDriver($this->getPDO());

        $this->assertIsArray($service->fetchAll());
        $this->assertCount(3, $service->fetchAll());
    }

    #[Test]
    public function fetchByAssembly()
    {
        $service = new Committee();
        $service->setDriver($this->getPDO());

        $service->fetchByAssembly(1);

        $this->assertCount(3, $service->fetchByAssembly(1));
    }

    #[Test]
    public function createSuccess()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
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

        $committee = (new Model\Committee())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(4);

        (new Committee())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createNegative()
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

        $committee = (new Model\Committee())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(-4);

        $service = new Committee();
        $service->setDriver($this->getPDO());
        $service->create($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveSuccess()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
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

        $committee = (new Model\Committee())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(4);

        (new Committee())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->getPDO())
            ->save($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveNegative()
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

        $committee = (new Model\Committee())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(-4);

        $service = new Committee();
        $service->setDriver($this->getPDO());
        $service->save($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveZeroId()
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

        $committee = (new Model\Committee())
            ->setFirstAssemblyId(1)
            ->setCommitteeId(0);

        $service = new Committee();
        $service->setDriver($this->getPDO());
        $service->save($committee);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function updateSuccess()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
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

        $committee = (new Model\Committee())
            ->setCommitteeId(1)
            ->setName('thisIsTheNewName')
            ->setFirstAssemblyId(1)
            ->setLastAssemblyId(2)
            ->setAbbrLong('com1')
            ->setAbbrShort('c1');

        $service = (new Committee())
            ->setDriver($this->getPDO())
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
}
