<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */

namespace Althingi\Service;

use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\Model\CommitteeMeetingAgenda as CommitteeMeetingAgendaModel;

class CommitteeMeetingAgendaTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $expectedData = (new CommitteeMeetingAgendaModel())
            ->setCommitteeMeetingId(1)
            ->setAssemblyId(1)
            ->setCommitteeMeetingAgendaId(1)
            ->setIssueId(1)
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
                ['committee_meeting_agenda_id' => 1, 'committee_meeting_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'title' => 'title'],
                ['committee_meeting_agenda_id' => 2, 'committee_meeting_id' => 1, 'issue_id' => null, 'assembly_id' => 1, 'title' => 'title'],
                ['committee_meeting_agenda_id' => 3, 'committee_meeting_id' => 1, 'issue_id' => null, 'assembly_id' => 1, 'title' => null],
                ['committee_meeting_agenda_id' => 4, 'committee_meeting_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'title' => 'thetitle'],
            ],
        ])->getTable('CommitteeMeetingAgenda');
        $actualTable = $this->getConnection()->createQueryTable('CommitteeMeetingAgenda', 'SELECT * FROM CommitteeMeetingAgenda');

        $committeeMeetingAgenda = (new CommitteeMeetingAgendaModel())
            ->setCommitteeMeetingId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setTitle('thetitle');


        $service = new CommitteeMeetingAgenda();
        $service->setDriver($this->pdo);
        $service->create($committeeMeetingAgenda);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $expectedTable = $this->createArrayDataSet([
            'CommitteeMeetingAgenda' => [
                ['committee_meeting_agenda_id' => 1, 'committee_meeting_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'title' => 'thisismynewtitle'],
                ['committee_meeting_agenda_id' => 2, 'committee_meeting_id' => 1, 'issue_id' => null, 'assembly_id' => 1, 'title' => 'title'],
                ['committee_meeting_agenda_id' => 3, 'committee_meeting_id' => 1, 'issue_id' => null, 'assembly_id' => 1, 'title' => null],
            ],
        ])->getTable('CommitteeMeetingAgenda');
        $actualTable = $this->getConnection()->createQueryTable('CommitteeMeetingAgenda', 'SELECT * FROM CommitteeMeetingAgenda');

        $committeeMeetingAgenda = (new CommitteeMeetingAgendaModel())
            ->setCommitteeMeetingAgendaId(1)
            ->setCommitteeMeetingId(1)
            ->setIssueId(1)
            ->setAssemblyId(1)
            ->setTitle('thisismynewtitle');


        $service = new CommitteeMeetingAgenda();
        $service->setDriver($this->pdo);
        $service->update($committeeMeetingAgenda);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        $this->pdo = new PDO(
            $GLOBALS['DB_DSN'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD'],
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]
        );
        return $this->createDefaultDBConnection($this->pdo);
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01'],
                ['assembly_id' => 2, 'from' => '2000-01-01'],
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1],
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
                ['committee_meeting_agenda_id' => 1, 'committee_meeting_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'title' => 'title'],
                ['committee_meeting_agenda_id' => 2, 'committee_meeting_id' => 1, 'issue_id' => null, 'assembly_id' => 1, 'title' => 'title'],
                ['committee_meeting_agenda_id' => 3, 'committee_meeting_id' => 1, 'issue_id' => null, 'assembly_id' => 1, 'title' => null],
            ],
        ]);
    }
}
