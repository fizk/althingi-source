<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use DateTime;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class PresidentTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());
        $expectedData = (new Model\President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setFrom(new DateTime('2000-01-01'))
            ->setTitle('t');
        $actualData = $presidentService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getNotFound()
    {
        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());
        $expectedData = null;
        $actualData = $presidentService->get(10987655);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getWithCongressman()
    {
        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());

        $expectedData = (new Model\PresidentCongressman())
            ->setPresidentId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setCongressmanId(1)
            ->setName('name1')
            ->setFrom(new DateTime('2000-01-01'))
            ->setBirth(new DateTime('2000-01-01'));
        $actualData = $presidentService->getWithCongressman(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getWithCongressmanNotFound()
    {
        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());

        $expectedData = null;
        $actualData = $presidentService->getWithCongressman(1000);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getByUnique()
    {
        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());

        $expectedData = (new Model\PresidentCongressman())
            ->setPresidentId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setCongressmanId(1)
            ->setName('name1')
            ->setFrom(new DateTime('2000-01-01'))
            ->setBirth(new DateTime('2000-01-01'));
        $actualData = $presidentService->getByUnique(1, 1, new DateTime('2000-01-01'), 't');

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllGeneratorAll()
    {
        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());
        $expectedData = [(new Model\President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setFrom(new DateTime('2000-01-01'))
            ->setTitle('t')];

        $actualData = [];
        foreach ($presidentService->fetchAllGenerator() as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllGeneratorByAssembly()
    {
        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());
        $expectedData = [(new Model\President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setFrom(new DateTime('2000-01-01'))
            ->setTitle('t')];

        $actualData = [];
        foreach ($presidentService->fetchAllGenerator(1) as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchAllGeneratorNotFound()
    {
        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());
        $expectedData = [];

        $actualData = [];
        foreach ($presidentService->fetchAllGenerator(2) as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        $president = (new Model\President())
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setFrom(new \DateTime('2001-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'President' => [
                [
                    'president_id' => 1,
                    'congressman_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01',
                    'to' => null,
                    'title' => 't',
                    'abbr' => null
                ], [
                    'president_id' => 2,
                    'congressman_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                    'title' => 't',
                    'abbr' => null
                ],
            ],
        ])->getTable('President');
        $actualTable = $this->getConnection()->createQueryTable('President', 'SELECT * FROM President');

        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());
        $presidentService->create($president);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createAlreadyExist()
    {
        $president = (new Model\President())
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setFrom(new \DateTime('2000-01-01'));

        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());
        try {
            $presidentService->create($president);
        } catch (\PDOException $e) {
            $this->assertEquals(1062, $e->errorInfo[1]);
        }
    }

    #[Test]
    public function updateSuccess()
    {
        $president = (new Model\President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('newTitle')
            ->setFrom(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'President' => [
                [
                    'president_id' => 1,
                    'congressman_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01',
                    'to' => null,
                    'title' => 'newTitle',
                    'abbr' => null
                ],
            ],
        ])->getTable('President');
        $actualTable = $this->getConnection()->createQueryTable('President', 'SELECT * FROM President');

        $presidentService = new President();
        $presidentService->setDriver($this->getPDO());
        $presidentService->update($president);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createFireEventResourceCreated()
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

        $president = (new Model\President())
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setFrom(new \DateTime('2001-01-01'));

        (new President())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($president)
        ;
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

        $president = (new Model\President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setFrom(new \DateTime('2000-01-01'));

        (new President())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($president)
        ;
    }

    #[Test]
    public function updateFireEventResourceFoundUpdateNeeded()
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

        $president = (new Model\President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setFrom(new \DateTime('2022-01-01'));

        (new President())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($president)
        ;
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null]
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name1', 'birth' => '2000-01-01'],
                ['congressman_id' => 2, 'name' => 'name2', 'birth' => '2000-01-01'],
                ['congressman_id' => 3, 'name' => 'name3', 'birth' => '2000-01-01'],
                ['congressman_id' => 4, 'name' => 'name4', 'birth' => '2000-01-01'],
            ],
            'President' => [
                ['president_id' => 1, 'congressman_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01', 'title' => 't']
            ]
        ]);
    }
}
