<?php

namespace AlthingiTest\Service;

use Althingi\Service\CommitteeMeetingAgenda;
use AlthingiTest\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\CommitteeMeetingAgenda as CommitteeMeetingAgendaModel;

class CommitteeMeetingAgendaTest extends TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $expectedData = (new CommitteeMeetingAgendaModel())
            ->setCommitteeMeetingId(1)
            ->setAssemblyId(1)
            ->setCommitteeMeetingAgendaId(1)
            ->setIssueId(1)
            ->setCategory('A')
            ->setTitle('title');
        $service = new CommitteeMeetingAgenda();
        $service->setDriver($this->pdo);

        $actualData = $service->get(1, 1);

        $this->assertEquals($actualData, $expectedData);
    }

    public function testCreate()
    {
        $expectedTable = $this->createArrayDataSet([
            'CommitteeMeetingAgenda' => [
                [
                    'committee_meeting_agenda_id' => 1, 'committee_meeting_id' => 1, 'issue_id' => 1,
                    'category' => 'A', 'assembly_id' => 1, 'title' => 'title',
                ], [
                    'committee_meeting_agenda_id' => 2, 'committee_meeting_id' => 1, 'issue_id' => null,
                    'category' => 'A', 'assembly_id' => 1, 'title' => 'title',
                ], [
                    'committee_meeting_agenda_id' => 3, 'committee_meeting_id' => 1, 'issue_id' => null,
                    'category' => 'A', 'assembly_id' => 1, 'title' => null,
                ], [
                    'committee_meeting_agenda_id' => 4, 'committee_meeting_id' => 1, 'issue_id' => 1,
                    'category' => 'A', 'assembly_id' => 1, 'title' => 'thetitle',
                ],
            ],
        ])->getTable('CommitteeMeetingAgenda');
        $actualTable = $this->getConnection()
            ->createQueryTable('CommitteeMeetingAgenda', 'SELECT * FROM CommitteeMeetingAgenda');

        $committeeMeetingAgenda = (new CommitteeMeetingAgendaModel())
            ->setCommitteeMeetingId(1)
            ->setIssueId(1)
            ->setCategory('A')
            ->setAssemblyId(1)
            ->setTitle('thetitle');


        $service = new CommitteeMeetingAgenda();
        $service->setDriver($this->pdo);
        $service->create($committeeMeetingAgenda);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $expectedTable = $this->createArrayDataSet([
            'CommitteeMeetingAgenda' => [
                [
                    'committee_meeting_agenda_id' => 1, 'committee_meeting_id' => 1, 'issue_id' => 1,
                    'category' => 'A', 'assembly_id' => 1, 'title' => 'title'
                ], [
                    'committee_meeting_agenda_id' => 2, 'committee_meeting_id' => 1, 'issue_id' => null,
                    'category' => 'A', 'assembly_id' => 1, 'title' => 'title'
                ], [
                    'committee_meeting_agenda_id' => 3, 'committee_meeting_id' => 1, 'issue_id' => null,
                    'category' => 'A', 'assembly_id' => 1, 'title' => null
                ], [
                    'committee_meeting_agenda_id' => 4, 'committee_meeting_id' => 1, 'issue_id' => 1,
                    'category' => 'A', 'assembly_id' => 1, 'title' => 'thetitle'
                ],
            ],
        ])->getTable('CommitteeMeetingAgenda');
        $actualTable = $this->getConnection()
            ->createQueryTable('CommitteeMeetingAgenda', 'SELECT * FROM CommitteeMeetingAgenda');

        $committeeMeetingAgenda = (new CommitteeMeetingAgendaModel())
            ->setCommitteeMeetingId(1)
            ->setIssueId(1)
            ->setCategory('A')
            ->setAssemblyId(1)
            ->setTitle('thetitle');


        $service = new CommitteeMeetingAgenda();
        $service->setDriver($this->pdo);
        $service->save($committeeMeetingAgenda);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $expectedTable = $this->createArrayDataSet([
            'CommitteeMeetingAgenda' => [
                [
                    'committee_meeting_agenda_id' => 1, 'committee_meeting_id' => 1, 'issue_id' => 1,
                    'category' => 'A', 'assembly_id' => 1, 'title' => 'thisismynewtitle'
                ], [
                    'committee_meeting_agenda_id' => 2, 'committee_meeting_id' => 1, 'issue_id' => null,
                    'category' => 'A', 'assembly_id' => 1, 'title' => 'title'
                ], [
                    'committee_meeting_agenda_id' => 3, 'committee_meeting_id' => 1, 'issue_id' => null,
                    'category' => 'A', 'assembly_id' => 1, 'title' => null
                ],
            ],
        ])->getTable('CommitteeMeetingAgenda');
        $actualTable = $this->getConnection()
            ->createQueryTable('CommitteeMeetingAgenda', 'SELECT * FROM CommitteeMeetingAgenda');

        $committeeMeetingAgenda = (new CommitteeMeetingAgendaModel())
            ->setCommitteeMeetingAgendaId(1)
            ->setCommitteeMeetingId(1)
            ->setIssueId(1)
            ->setCategory('A')
            ->setAssemblyId(1)
            ->setTitle('thisismynewtitle');


        $service = new CommitteeMeetingAgenda();
        $service->setDriver($this->pdo);
        $service->update($committeeMeetingAgenda);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01'],
                ['assembly_id' => 2, 'from' => '2000-01-01'],
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1, 'category' => 'A'],
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
            ],
            'CommitteeMeetingAgenda' => [
                [
                    'committee_meeting_agenda_id' => 1, 'committee_meeting_id' => 1,
                    'issue_id' => 1, 'assembly_id' => 1, 'title' => 'title', 'category' => 'A'
                ], [
                    'committee_meeting_agenda_id' => 2, 'committee_meeting_id' => 1,
                    'issue_id' => null, 'assembly_id' => 1, 'title' => 'title', 'category' => 'A'
                ], [
                    'committee_meeting_agenda_id' => 3, 'committee_meeting_id' => 1,
                    'issue_id' => null, 'assembly_id' => 1, 'title' => null, 'category' => 'A'
                ],
            ],
        ]);
    }
}
