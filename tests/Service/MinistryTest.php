<?php

namespace Althingi\Service;

use Althingi\Service\Ministry;
use Althingi\DatabaseConnection;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\TestCase;
use PDO;
use Psr\EventDispatcher\EventDispatcherInterface;

class MinistryTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->pdo);

        $expectedData = (new Model\Ministry())
            ->setMinistryId(1)
            ->setName('name 1')
            ->setAbbrShort('abbr_short1')
            ->setAbbrLong('abbr_long1')
            ->setFirst(1)
            ->setLast(3);

        $actualData = $ministryService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetNotFound()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->pdo);

        $actualData = $ministryService->get(100);

        $this->assertNull($actualData);
    }

    public function testFetch()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->pdo);

        $expectedData = [
            (new Model\Ministry())
                ->setMinistryId(1)
                ->setName('name 1')
                ->setAbbrShort('abbr_short1')
                ->setAbbrLong('abbr_long1')
                ->setFirst(1)
                ->setLast(3),
            (new Model\Ministry())
                ->setMinistryId(2)
                ->setName('name 2')
                ->setAbbrShort('abbr_short2')
                ->setAbbrLong('abbr_long2')
                ->setFirst(1)
                ->setLast(null)
        ];
        $actualData = $ministryService->fetchAll();

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllGenerator()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->pdo);

        $expectedData = [
            (new Model\Ministry())
                ->setMinistryId(1)
                ->setName('name 1')
                ->setAbbrShort('abbr_short1')
                ->setAbbrLong('abbr_long1')
                ->setFirst(1)
                ->setLast(3),
            (new Model\Ministry())
                ->setMinistryId(2)
                ->setName('name 2')
                ->setAbbrShort('abbr_short2')
                ->setAbbrLong('abbr_long2')
                ->setFirst(1)
                ->setLast(null)
        ];
        $actualData = [];
        foreach ($ministryService->fetchAllGenerator() as $ministry) {
            $actualData[] = $ministry;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $ministry = (new Model\Ministry())
            ->setMinistryId(3)
            ->setName('name 3')
            ->setAbbrShort('abbr_short3')
            ->setAbbrLong('abbr_long3');

        $expectedTable = $this->createArrayDataSet([
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
                [
                    'ministry_id' => 3,
                    'name' => 'name 3',
                    'abbr_short' => 'abbr_short3',
                    'abbr_long' => 'abbr_long3',
                    'first' => null,
                    'last' => null,
                ],
            ],
        ])->getTable('Ministry');
        $actualTable = $this->getConnection()->createQueryTable('Ministry', 'SELECT * FROM Ministry');

        $ministryService = new Ministry();
        $ministryService->setDriver($this->pdo);
        $ministryService->create($ministry);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSaveUpdate()
    {
        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('new name')
            ->setAbbrShort('new abbr short')
            ->setAbbrLong('new abbr long')
            ->setFirst(1)
        ;

        $expectedTable = $this->createArrayDataSet([
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
                    'name' => 'new name',
                    'abbr_short' => 'new abbr short',
                    'abbr_long' => 'new abbr long',
                    'first' => 1,
                    'last' => null,
                ],
            ],
        ])->getTable('Ministry');
        $actualTable = $this->getConnection()->createQueryTable('Ministry', 'SELECT * FROM Ministry');

        $ministryService = new Ministry();
        $ministryService->setDriver($this->pdo);
        $affectedRows = $ministryService->save($ministry);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(2, $affectedRows);
    }

    public function testSaveCreate()
    {
        $ministry = (new Model\Ministry())
            ->setMinistryId(3)
            ->setName('new name')
            ->setAbbrShort('new abbr short')
            ->setAbbrLong('new abbr long')
            ->setFirst(1)
        ;

        $expectedTable = $this->createArrayDataSet([
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
                [
                    'ministry_id' => 3,
                    'name' => 'new name',
                    'abbr_short' => 'new abbr short',
                    'abbr_long' => 'new abbr long',
                    'first' => 1,
                    'last' => null,
                ],
            ],
        ])->getTable('Ministry');
        $actualTable = $this->getConnection()->createQueryTable('Ministry', 'SELECT * FROM Ministry');

        $ministryService = new Ministry();
        $ministryService->setDriver($this->pdo);
        $affectedRows = $ministryService->save($ministry);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    public function testUpdate()
    {
        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('new name')
            ->setAbbrShort('new abbr short')
            ->setAbbrLong('new abbr long')
            ->setFirst(1)
        ;

        $expectedTable = $this->createArrayDataSet([
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
                    'name' => 'new name',
                    'abbr_short' => 'new abbr short',
                    'abbr_long' => 'new abbr long',
                    'first' => 1,
                    'last' => null,
                ],
            ],
        ])->getTable('Ministry');
        $actualTable = $this->getConnection()->createQueryTable('Ministry', 'SELECT * FROM Ministry');

        $ministryService = new Ministry();
        $ministryService->setDriver($this->pdo);
        $ministryService->update($ministry);


        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testCount()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->pdo);

        $this->assertEquals(2, $ministryService->count());
    }

    public function testCreateFireEventCreateResource()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $ministry = (new Model\Ministry())
            ->setMinistryId(3)
            ->setName('name 3')
            ->setAbbrShort('abbr_short3')
            ->setAbbrLong('abbr_long3');

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->pdo)
            ->create($ministry);

    }

    public function testUpdateFireEventResourceFoundUpdateRequired()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('name 2')
            ->setAbbrShort('abbr_short2-update')
            ->setAbbrLong('abbr_long2');

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->pdo)
            ->update($ministry);
    }

    public function testUpdateFireEventResourceFoundNoUpdateNeeded()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('name 2')
            ->setAbbrShort('abbr_short2')
            ->setAbbrLong('abbr_long2')
            ->setFirst(1);

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->pdo)
            ->update($ministry);
    }

    public function testSaveFireEventResourceCreated()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $ministry = (new Model\Ministry())
            ->setMinistryId(3)
            ->setName('name 3')
            ->setAbbrShort('abbr_short3')
            ->setAbbrLong('abbr_long3')
            ->setFirst(1);

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->pdo)
            ->save($ministry);
    }

    public function testSaveFireEventResourceFoundNoUpdateNeeded()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('name 2')
            ->setAbbrShort('abbr_short2')
            ->setAbbrLong('abbr_long2')
            ->setFirst(1);

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->pdo)
            ->save($ministry);
    }

    public function testSaveFireEventResourceFoundUpdateRequired()
    {
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('name 2')
            ->setAbbrShort('abbr_short2-update')
            ->setAbbrLong('abbr_long2')
            ->setFirst(1);

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->pdo)
            ->save($ministry);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
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
        ]);
    }
}
