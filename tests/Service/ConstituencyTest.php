<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test, After};
use PHPUnit\Framework\TestCase;

class ConstituencyTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[After]
    public function down(): void
    {
        Mockery::close();
    }

    #[Test]
    public function getsuccess()
    {
        $service = new Constituency();
        $service->setDriver($this->getPDO());

        $expectedData = (new Model\Constituency())
            ->setConstituencyId(1)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none');
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof AddEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new Model\Constituency())
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
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($constituency);

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

        $constituency = (new Model\Constituency())
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
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($constituency);

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

        $constituency = (new Model\Constituency())
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
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($constituency);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createFireEventOneCreatedNewEntry()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new Model\Constituency())
            ->setName('name')
            ->setConstituencyId(2);

        (new Constituency())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($constituency);
    }

    #[Test]
    public function updateFireEventZeroFoundEntryButNoUpdateRequired()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new Model\Constituency())
            ->setConstituencyId(1)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($constituency);
    }

    #[Test]
    public function updateFireEventOneFoundEntryAndUpdated()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new Model\Constituency())
            ->setConstituencyId(1)
            ->setName('some-place-update')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($constituency);
    }

    #[Test]
    public function saveFireEventZeroFoundButNoUpdate()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new Model\Constituency())
            ->setConstituencyId(1)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($constituency);
    }

    #[Test]
    public function saveFireEventOneCreatedNewentry()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new Model\Constituency())
            ->setConstituencyId(2)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($constituency);
    }

    #[Test]
    public function saveFireEventTwoEntryFoundAndUpdated()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->with(Mockery::on(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            }))
            ->times(1)
            ->getMock();

        $constituency = (new Model\Constituency())
            ->setConstituencyId(1)
            ->setName('some-place-update')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none')
            ;

        (new Constituency())
            ->setDriver($this->getPDO())
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
}
