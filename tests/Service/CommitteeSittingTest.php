<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class CommitteeSittingTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $committeeSitting = new CommitteeSitting();
        $committeeSitting->setDriver($this->getPDO());

        $expectedData = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(1)
            ->setAssemblyId(4)
            ->setCongressmanId(2)
            ->setCommitteeId(3)
            ->setRole('role')
            ->setOrder(5)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'));

        $actualData = $committeeSitting->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByCongressman()
    {
        $committeeSitting = new CommitteeSitting();
        $committeeSitting->setDriver($this->getPDO());

        $expectedData = [
            (new Model\CommitteeSitting())
                ->setCommitteeSittingId(1)
                ->setAssemblyId(4)
                ->setCongressmanId(2)
                ->setCommitteeId(3)
                ->setRole('role')
                ->setOrder(5)
                ->setFrom(new \DateTime('2001-01-01'))
                ->setTo(new \DateTime('2001-01-01'))
        ]
        ;

        $actualData = $committeeSitting->fetchByCongressman(2);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByCongressmanNotFound()
    {
        $committeeSitting = new CommitteeSitting();
        $committeeSitting->setDriver($this->getPDO());

        $expectedData = [];

        $actualData = $committeeSitting->fetchByCongressman(200);

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

        $sitting = (new Model\CommitteeSitting())
            ->setAssemblyId(4)
            ->setCongressmanId(2)
            ->setCommitteeId(3)
            ->setRole('role')
            ->setOrder(5)
            ->setFrom(new \DateTime('2001-01-02'))
            ->setTo(new \DateTime('2001-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'CommitteeSitting' => [
                [
                    'committee_sitting_id' => 1,
                    'congressman_id' => 2,
                    'committee_id' => 3,
                    'assembly_id' => 4,
                    'order' => 5,
                    'role' => 'role',
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ],
                [
                    'committee_sitting_id' => 2,
                    'congressman_id' => 2,
                    'committee_id' => 3,
                    'assembly_id' => 4,
                    'order' => 5,
                    'role' => 'role',
                    'from' => '2001-01-02',
                    'to' => '2001-01-01',
                ],
            ]
        ])->getTable('CommitteeSitting');
        $actualTable = $this->getConnection()->createQueryTable(
            'CommitteeSitting',
            'SELECT * FROM CommitteeSitting'
        );

        $committeeSitting = new CommitteeSitting();
        $committeeSitting->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $committeeSitting->create($sitting);

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

        $sitting = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(1)
            ->setAssemblyId(4)
            ->setCongressmanId(2)
            ->setCommitteeId(3)
            ->setRole('role')
            ->setOrder(5)
            ->setFrom(new \DateTime('2001-01-02'))
            ->setTo(new \DateTime('2011-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'CommitteeSitting' => [
                [
                    'committee_sitting_id' => 1,
                    'congressman_id' => 2,
                    'committee_id' => 3,
                    'assembly_id' => 4,
                    'order' => 5,
                    'role' => 'role',
                    'from' => '2001-01-02',
                    'to' => '2011-01-01',
                ],
            ]
        ])->getTable('CommitteeSitting');
        $actualTable = $this->getConnection()->createQueryTable(
            'CommitteeSitting',
            'SELECT * FROM CommitteeSitting'
        );

        $committeeSitting = new CommitteeSitting();
        $committeeSitting->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $committeeSitting->update($sitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createFireEventOne()
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

        $sitting = (new Model\CommitteeSitting())
            ->setAssemblyId(4)
            ->setCongressmanId(2)
            ->setCommitteeId(3)
            ->setRole('role')
            ->setOrder(5)
            ->setFrom(new \DateTime('2001-01-02'))
            ->setTo(new \DateTime('2001-01-01'));


        $committeeSitting = new CommitteeSitting();
        $committeeSitting->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $committeeSitting->create($sitting);
    }

    #[Test]
    public function updateFireEventZero()
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

        $sitting = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(1)
            ->setAssemblyId(4)
            ->setCongressmanId(2)
            ->setCommitteeId(3)
            ->setRole('role')
            ->setOrder(5)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;

        $committeeSitting = new CommitteeSitting();
        $committeeSitting->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $committeeSitting->update($sitting);
    }

    #[Test]
    public function updateFireEventOne()
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

        $sitting = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(1)
            ->setAssemblyId(4)
            ->setCongressmanId(2)
            ->setCommitteeId(3)
            ->setRole('role')
            ->setOrder(5)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2002-01-01'))
        ;

        $committeeSitting = new CommitteeSitting();
        $committeeSitting->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher);
        $committeeSitting->update($sitting);
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
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name1', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 2, 'name' => 'name2', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name3', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 4, 'name' => 'name4', 'birth' => '2000-01-01', 'death' => null],
            ],
            'Committee' => [
                [
                    'committee_id' => 1,
                    'name' => 'name 1',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short'
                ],
                [
                    'committee_id' => 3,
                    'name' => 'name 3',
                    'first_assembly_id' => 1,
                    'last_assembly_id' => 2,
                    'abbr_long' => 'abbr_long',
                    'abbr_short' => 'abbr_short'
                ],
            ],
            'CommitteeSitting' => [
                [
                    'committee_sitting_id' => 1,
                    'congressman_id' => 2,
                    'committee_id' => 3,
                    'assembly_id' => 4,
                    'order' => 5,
                    'role' => 'role',
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ]
            ]
        ]);
    }
}
