<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class MinistryTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->getPDO());

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

    #[Test]
    public function getNotFound()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->getPDO());

        $actualData = $ministryService->get(100);

        $this->assertNull($actualData);
    }

    #[Test]
    public function fetchSuccess()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->getPDO());

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

    #[Test]
    public function fetchAllGenerator()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->getPDO());

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

    #[Test]
    public function create()
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
        $ministryService->setDriver($this->getPDO());
        $ministryService->create($ministry);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveUpdate()
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
        $ministryService->setDriver($this->getPDO());
        $affectedRows = $ministryService->save($ministry);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(2, $affectedRows);
    }

    #[Test]
    public function saveCreate()
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
        $ministryService->setDriver($this->getPDO());
        $affectedRows = $ministryService->save($ministry);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    #[Test]
    public function updateSuccess()
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
        $ministryService->setDriver($this->getPDO());
        $ministryService->update($ministry);


        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function countSuccess()
    {
        $ministryService = new Ministry();
        $ministryService->setDriver($this->getPDO());

        $this->assertEquals(2, $ministryService->count());
    }

    #[Test]
    public function createFireEventCreateResource()
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

        $ministry = (new Model\Ministry())
            ->setMinistryId(3)
            ->setName('name 3')
            ->setAbbrShort('abbr_short3')
            ->setAbbrLong('abbr_long3');

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->getPDO())
            ->create($ministry);
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

        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('name 2')
            ->setAbbrShort('abbr_short2-update')
            ->setAbbrLong('abbr_long2');

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->getPDO())
            ->update($ministry);
    }

    #[Test]
    public function updateFireEventResourceFoundNoUpdateNeeded()
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

        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('name 2')
            ->setAbbrShort('abbr_short2')
            ->setAbbrLong('abbr_long2')
            ->setFirst(1);

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->getPDO())
            ->update($ministry);
    }

    #[Test]
    public function saveFireEventResourceCreated()
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

        $ministry = (new Model\Ministry())
            ->setMinistryId(3)
            ->setName('name 3')
            ->setAbbrShort('abbr_short3')
            ->setAbbrLong('abbr_long3')
            ->setFirst(1);

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->getPDO())
            ->save($ministry);
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

        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('name 2')
            ->setAbbrShort('abbr_short2')
            ->setAbbrLong('abbr_long2')
            ->setFirst(1);

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->getPDO())
            ->save($ministry);
    }

    #[Test]
    public function saveFireEventResourceFoundUpdateRequired()
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

        $ministry = (new Model\Ministry())
            ->setMinistryId(2)
            ->setName('name 2')
            ->setAbbrShort('abbr_short2-update')
            ->setAbbrLong('abbr_long2')
            ->setFirst(1);

        (new Ministry())
            ->setEventDispatcher($eventDispatcher)
            ->setDriver($this->getPDO())
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
