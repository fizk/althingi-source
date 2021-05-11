<?php

namespace Althingi\Service;

use Althingi\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Service;
use Althingi\Model;
use Althingi\Events\{UpdateEvent, AddEvent};
use Psr\EventDispatcher\EventDispatcherInterface;
use Mockery;
use PDO;

class CommitteeSittingTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $committeeSitting = new Service\CommitteeSitting();
        $committeeSitting->setDriver($this->pdo);

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

    public function testFetchByCongressman()
    {
        $committeeSitting = new Service\CommitteeSitting();
        $committeeSitting->setDriver($this->pdo);

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

    public function testFetchByCongressmanNotFound()
    {
        $committeeSitting = new Service\CommitteeSitting();
        $committeeSitting->setDriver($this->pdo);

        $expectedData = [];

        $actualData = $committeeSitting->fetchByCongressman(200);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
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

        $committeeSitting = new Service\CommitteeSitting();
        $committeeSitting->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $committeeSitting->create($sitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
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

        $committeeSitting = new Service\CommitteeSitting();
        $committeeSitting->setDriver($this->pdo)
            ->setEventDispatcher($eventDispatcher);
        $committeeSitting->update($sitting);

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
