<?php

namespace Althingi\Service;

use PHPUnit\Framework\TestCase;
use Althingi\Service\CommitteeMeeting;
use Althingi\DatabaseConnection;
use Althingi\Model\CommitteeMeeting as CommitteeMeetingModel;
use PDO;

class CommitteeMeetingTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $expectedData = (new CommitteeMeetingModel())
            ->setCommitteeMeetingId(1)
            ->setCommitteeId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2000-01-01 00:00'));

        $service = new CommitteeMeeting();
        $service->setDriver($this->pdo);

        $this->assertEquals($expectedData, $service->get(1));
    }

    public function testFetch()
    {
        $service = new CommitteeMeeting();
        $service->setDriver($this->pdo);

        $committees = $service->fetchByAssembly(1, 1);

        $this->assertCount(3, $committees);
        $this->assertInstanceOf(CommitteeMeetingModel::class, $committees[0]);
    }

    public function testCreate()
    {
        $expectedTable = $this->createArrayDataSet([
            'CommitteeMeeting' => [
                [
                    'committee_meeting_id' => 1,
                    'committee_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 2,
                    'committee_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 3,
                    'committee_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 4,
                    'committee_id' => 1,
                    'assembly_id' => 2,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 5,
                    'committee_id' => 1,
                    'assembly_id' => 2,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 6,
                    'committee_id' => 1,
                    'assembly_id' => 2,
                    'from' => null,
                    'to' => null,
                    'description' => null
                ],
            ],
        ])->getTable('CommitteeMeeting');
        $actualTable = $this->getConnection()->createQueryTable('CommitteeMeeting', 'SELECT * FROM CommitteeMeeting');

        $committeeMeeting = (new CommitteeMeetingModel())
            ->setCommitteeId(1)
            ->setAssemblyId(2)
            ->setCommitteeMeetingId(6);

        $service = new CommitteeMeeting();
        $service->setDriver($this->pdo);
        $service->create($committeeMeeting);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $expectedTable = $this->createArrayDataSet([
            'CommitteeMeeting' => [
                [
                    'committee_meeting_id' => 1,
                    'committee_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 2,
                    'committee_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 3,
                    'committee_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 4,
                    'committee_id' => 1,
                    'assembly_id' => 2,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 5,
                    'committee_id' => 1,
                    'assembly_id' => 2,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 6,
                    'committee_id' => 1,
                    'assembly_id' => 2,
                    'from' => null,
                    'to' => null,
                    'description' => null
                ],
            ],
        ])->getTable('CommitteeMeeting');
        $actualTable = $this->getConnection()->createQueryTable('CommitteeMeeting', 'SELECT * FROM CommitteeMeeting');

        $committeeMeeting = (new CommitteeMeetingModel())
            ->setCommitteeId(1)
            ->setAssemblyId(2)
            ->setCommitteeMeetingId(6);

        $service = new CommitteeMeeting();
        $service->setDriver($this->pdo);
        $service->save($committeeMeeting);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $expectedTable = $this->createArrayDataSet([
            'CommitteeMeeting' => [
                [
                    'committee_meeting_id' => 1,
                    'committee_id' => 1,
                    'assembly_id' => 1,
                    'from' => null,
                    'to' => null,
                    'description' => 'description'
                ], [
                    'committee_meeting_id' => 2,
                    'committee_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 3,
                    'committee_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 4,
                    'committee_id' => 1,
                    'assembly_id' => 2,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ], [
                    'committee_meeting_id' => 5,
                    'committee_id' => 1,
                    'assembly_id' => 2,
                    'from' => '2000-01-01 00:00:00',
                    'to' => null,
                    'description' => null
                ],
            ],
        ])->getTable('CommitteeMeeting');
        $actualTable = $this->getConnection()->createQueryTable('CommitteeMeeting', 'SELECT * FROM CommitteeMeeting');

        $committeeMeeting = (new CommitteeMeetingModel())
            ->setCommitteeMeetingId(1)
            ->setCommitteeId(1)
            ->setAssemblyId(1)
            ->setDescription('description');

        $service = new CommitteeMeeting();
        $service->setDriver($this->pdo);
        $service->update($committeeMeeting);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01'],
                ['assembly_id' => 2, 'from' => '2000-01-01'],
            ],
            'Committee' => [
                ['committee_id' => 1, 'name' => 'committee1', 'first_assembly_id' => 1],
                ['committee_id' => 2, 'name' => 'committee2', 'first_assembly_id' => 1],
                ['committee_id' => 3, 'name' => null, 'first_assembly_id' => 1],
                ['committee_id' => 4, 'name' => null, 'first_assembly_id' => 1],
            ],
            'CommitteeMeeting' => [
                ['committee_meeting_id' => 1, 'committee_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00'],
                ['committee_meeting_id' => 2, 'committee_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00'],
                ['committee_meeting_id' => 3, 'committee_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01 00:00'],
                ['committee_meeting_id' => 4, 'committee_id' => 1, 'assembly_id' => 2, 'from' => '2000-01-01 00:00'],
                ['committee_meeting_id' => 5, 'committee_id' => 1, 'assembly_id' => 2, 'from' => '2000-01-01 00:00'],
            ]
        ]);
    }
}
