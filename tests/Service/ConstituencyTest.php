<?php

namespace Althingi\Service;

use Althingi\Service\Constituency;
use Althingi\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Constituency as ConstituencyModel;
use Althingi\Events\{UpdateEvent, AddEvent};
use Mockery;
use PDO;

class ConstituencyTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $service = new Constituency();
        $service->setDriver($this->pdo);

        $expectedData = (new ConstituencyModel)
            ->setConstituencyId(1)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none');
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof AddEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new ConstituencyModel())
            ->setName('name')
            ->setConstituencyId(2);

        $expectedTable = $this->createArrayDataSet([
            'Constituency' => [
                [
                    'constituency_id' => 1,
                    'name' => 'some-place',
                    'abbr_short' => 's-p',
                    'abbr_long' => 'so-pl',
                    'description' => 'none'
                ], [
                    'constituency_id' => 2,
                    'name' => 'name',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null
                ],
            ],
        ])->getTable('Constituency');
        $actualTable = $this->getConnection()->createQueryTable('Constituency', 'SELECT * FROM Constituency');

        (new Constituency())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->create($constituency);

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

        $constituency = (new ConstituencyModel())
            ->setName('name')
            ->setConstituencyId(2);

        $expectedTable = $this->createArrayDataSet([
            'Constituency' => [
                [
                    'constituency_id' => 1,
                    'name' => 'some-place',
                    'abbr_short' => 's-p',
                    'abbr_long' => 'so-pl',
                    'description' => 'none'
                ], [
                    'constituency_id' => 2,
                    'name' => 'name',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null
                ],
            ],
        ])->getTable('Constituency');
        $actualTable = $this->getConnection()->createQueryTable('Constituency', 'SELECT * FROM Constituency');

        (new Constituency())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($constituency);

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

        $constituency = (new ConstituencyModel())
            ->setConstituencyId(1)
            ->setName('another-place');

        $expectedTable = $this->createArrayDataSet([
            'Constituency' => [
                [
                    'constituency_id' => 1,
                    'name' => 'another-place',
                    'abbr_short' => null,
                    'abbr_long' => null,
                    'description' => null
                ],
            ],
        ])->getTable('Constituency');
        $actualTable = $this->getConnection()->createQueryTable('Constituency', 'SELECT * FROM Constituency');

        (new Constituency())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($constituency);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testCreateFireEventOneCreatedNewEntry()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new ConstituencyModel())
            ->setName('name')
            ->setConstituencyId(2);

        (new Constituency())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->create($constituency);
    }

    public function testUpdateFireEventZeroFoundEntryButNoUpdateRequired()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new ConstituencyModel())
            ->setConstituencyId(1)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($constituency);
    }

    public function testUpdateFireEventOneFoundEntryAndUpdated()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new ConstituencyModel())
            ->setConstituencyId(1)
            ->setName('some-place-update')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->update($constituency);
    }

    public function testSaveFireEventZeroFoundButNoUpdate()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new ConstituencyModel())
            ->setConstituencyId(1)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($constituency);
    }

    public function testSaveFireEventOneCreatedNewentry()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new ConstituencyModel())
            ->setConstituencyId(2)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($constituency);
    }

    public function testSaveFireEventTwoEntryFoundAndUpdated()
    {
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new ConstituencyModel())
            ->setConstituencyId(1)
            ->setName('some-place-update')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher)
            ->save($constituency);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Constituency' => [
                [
                    'constituency_id' => 1,
                    'name' => 'some-place',
                    'abbr_short' => 's-p',
                    'abbr_long' => 'so-pl',
                    'description' => 'none'
                ]
            ],
        ]);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
