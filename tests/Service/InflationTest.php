<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use DateTime;
use Mockery;
use PHPUnit\Framework\Attributes\{Test, After};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class InflationTest extends TestCase
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
        $inflationService = new Inflation();
        $inflationService->setDriver($this->getPDO());

        $expectedData = (new Model\Inflation())
            ->setId(1)
            ->setDate(new DateTime('2000-01-01'))
            ->setValue(1);

        $actualData = $inflationService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAll()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Inflation())->setId(1)->setDate(new DateTime('2000-01-01'))->setValue(1),
            (new Model\Inflation())->setId(2)->setDate(new DateTime('2000-01-02'))->setValue(2),
            (new Model\Inflation())->setId(3)->setDate(new DateTime('2000-01-03'))->setValue(3),
            (new Model\Inflation())->setId(4)->setDate(new DateTime('2000-01-04'))->setValue(4),
            (new Model\Inflation())->setId(5)->setDate(new DateTime('2000-01-05'))->setValue(5),
        ];

        $actualData = $inflationService->fetchAll();

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllGenerator()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Inflation())->setId(1)->setDate(new DateTime('2000-01-01'))->setValue(1),
            (new Model\Inflation())->setId(2)->setDate(new DateTime('2000-01-02'))->setValue(2),
            (new Model\Inflation())->setId(3)->setDate(new DateTime('2000-01-03'))->setValue(3),
            (new Model\Inflation())->setId(4)->setDate(new DateTime('2000-01-04'))->setValue(4),
            (new Model\Inflation())->setId(5)->setDate(new DateTime('2000-01-05'))->setValue(5),
        ];

        $actualData = [];
        foreach ($inflationService->fetchAllGenerator() as $inflation) {
            $actualData[] = $inflation;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllFrom()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Inflation())->setId(3)->setDate(new DateTime('2000-01-03'))->setValue(3),
            (new Model\Inflation())->setId(4)->setDate(new DateTime('2000-01-04'))->setValue(4),
            (new Model\Inflation())->setId(5)->setDate(new DateTime('2000-01-05'))->setValue(5),
        ];

        $actualData = $inflationService->fetchAll(new DateTime('2000-01-03'));

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllTo()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Inflation())->setId(1)->setDate(new DateTime('2000-01-01'))->setValue(1),
            (new Model\Inflation())->setId(2)->setDate(new DateTime('2000-01-02'))->setValue(2),
            (new Model\Inflation())->setId(3)->setDate(new DateTime('2000-01-03'))->setValue(3),
        ];

        $actualData = $inflationService->fetchAll(null, new DateTime('2000-01-03'));

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllFromAndTo()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->getPDO());

        $expectedData = [
            (new Model\Inflation())->setId(2)->setDate(new DateTime('2000-01-02'))->setValue(2),
            (new Model\Inflation())->setId(3)->setDate(new DateTime('2000-01-03'))->setValue(3),
            (new Model\Inflation())->setId(4)->setDate(new DateTime('2000-01-04'))->setValue(4),
        ];

        $actualData = $inflationService->fetchAll(new DateTime('2000-01-02'), new DateTime('2000-01-04'));

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function saveUpdate()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $inflation = (new Model\Inflation())
            ->setId(2)
            ->setDate(new DateTime('2000-01-02'))
            ->setValue(20);

        $expectedTable = $this->createArrayDataSet([
            'Inflation' => [
                ['id' => 1, 'date' => '2000-01-01', 'value' => 1],
                ['id' => 2, 'date' => '2000-01-02', 'value' => 20],
                ['id' => 3, 'date' => '2000-01-03', 'value' => 3],
                ['id' => 4, 'date' => '2000-01-04', 'value' => 4],
                ['id' => 5, 'date' => '2000-01-05', 'value' => 5],
            ],
        ])->getTable('Inflation');
        $actualTable = $this->getConnection()->createQueryTable('Inflation', 'SELECT * FROM Inflation');

        $affectedRows = (new Inflation())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($inflation);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(2, $affectedRows);
    }

    #[Test]
    public function saveCreate()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof AddEvent;
            })
            ->getMock();

        $inflation = (new Model\Inflation())
            ->setId(6)
            ->setDate(new DateTime('2000-01-06'))
            ->setValue(6);

        $expectedTable = $this->createArrayDataSet([
            'Inflation' => [
                ['id' => 1, 'date' => '2000-01-01', 'value' => 1],
                ['id' => 2, 'date' => '2000-01-02', 'value' => 2],
                ['id' => 3, 'date' => '2000-01-03', 'value' => 3],
                ['id' => 4, 'date' => '2000-01-04', 'value' => 4],
                ['id' => 5, 'date' => '2000-01-05', 'value' => 5],
                ['id' => 6, 'date' => '2000-01-06', 'value' => 6],
            ],
        ])->getTable('Inflation');
        $actualTable = $this->getConnection()->createQueryTable('Inflation', 'SELECT * FROM Inflation');

        $affectedRows = (new Inflation())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($inflation);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    #[Test]
    public function update()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function ($args) {
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $inflation = (new Model\Inflation())
            ->setId(2)
            ->setDate(new DateTime('2000-01-02'))
            ->setValue(20);

        $expectedTable = $this->createArrayDataSet([
            'Inflation' => [
                ['id' => 1, 'date' => '2000-01-01', 'value' => 1],
                ['id' => 2, 'date' => '2000-01-02', 'value' => 20],
                ['id' => 3, 'date' => '2000-01-03', 'value' => 3],
                ['id' => 4, 'date' => '2000-01-04', 'value' => 4],
                ['id' => 5, 'date' => '2000-01-05', 'value' => 5],
            ],
        ])->getTable('Inflation');
        $actualTable = $this->getConnection()->createQueryTable('Inflation', 'SELECT * FROM Inflation');

        $affectedRows = (new Inflation())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($inflation);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    #[Test]
    public function updateFireEventsEntryFoundButNoUpdatesRequired()
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

        $inflation = (new Model\Inflation())
            ->setId(1)
            ->setDate(new DateTime('2000-01-01'))
            ->setValue(1);

        (new Inflation())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($inflation);
    }

    #[Test]
    public function updateFireEventsEntryFoundAndUpdateIsRequired()
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

        $inflation = (new Model\Inflation())
            ->setId(1)
            ->setDate(new DateTime('2000-01-01'))
            ->setValue(2);

        (new Inflation())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($inflation);
    }

    #[Test]
    public function saveFireEventsEntryCreated()
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

        $inflation = (new Model\Inflation())
            ->setId(6)
            ->setDate(new DateTime('2000-01-01'))
            ->setValue(2);

        (new Inflation())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($inflation);
    }

    #[Test]
    public function saveFireEventsEntryFoundButNoUpdateNeeded()
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

        $inflation = (new Model\Inflation())
            ->setId(1)
            ->setDate(new DateTime('2000-01-01'))
            ->setValue(1);

        (new Inflation())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($inflation);
    }

    #[Test]
    public function saveFireEventsEntryFoundAndAnUpdateRequied()
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

        $inflation = (new Model\Inflation())
            ->setId(1)
            ->setDate(new DateTime('2000-01-01'))
            ->setValue(2);

        (new Inflation())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($inflation);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Inflation' => [
                ['id' => 1, 'date' => '2000-01-01', 'value' => 1],
                ['id' => 2, 'date' => '2000-01-02', 'value' => 2],
                ['id' => 3, 'date' => '2000-01-03', 'value' => 3],
                ['id' => 4, 'date' => '2000-01-04', 'value' => 4],
                ['id' => 5, 'date' => '2000-01-05', 'value' => 5],
            ],
        ]);
    }
}
