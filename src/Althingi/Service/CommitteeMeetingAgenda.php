<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\CommitteeMeetingAgenda as CommitteeMeetingAgendaModel;
use Althingi\Hydrator\CommitteeMeetingAgenda as CommitteeMeetingAgendaHydrator;
use PDO;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class CommitteeMeetingAgenda implements DatabaseAwareInterface
{
    use DatabaseService;

    /** @var  \PDO */
    private $pdo;

    /**
     * @param $meetingId
     * @param $agendaId
     * @return \Althingi\Model\CommitteeMeetingAgenda|null
     */
    public function get(int $meetingId, int $agendaId): ?CommitteeMeetingAgendaModel
    {
        $statement = $this->getDriver()->prepare('
            select * from `CommitteeMeetingAgenda` C 
            where C.`committee_meeting_id` = :committee_meeting_id 
              and C.`committee_meeting_agenda_id` = :committee_meeting_agenda_id;
        ');
        $statement->execute([
            'committee_meeting_id' => $meetingId,
            'committee_meeting_agenda_id' => $agendaId
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new CommitteeMeetingAgendaHydrator())->hydrate($object, new CommitteeMeetingAgendaModel())
            : null;
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\CommitteeMeetingAgenda $data
     * @return int affected rows
     */
    public function create(CommitteeMeetingAgendaModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('CommitteeMeetingAgenda', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\CommitteeMeetingAgenda $data
     * @return int affected rows
     */
    public function save(CommitteeMeetingAgendaModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('CommitteeMeetingAgenda', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\CommitteeMeetingAgenda $data
     * @return int affected rows
     */
    public function update(CommitteeMeetingAgendaModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'CommitteeMeetingAgenda',
                $data,
                "committee_meeting_id={$data->getCommitteeMeetingId()} and committee_meeting_agenda_id={$data->getCommitteeMeetingAgendaId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * @param \PDO $pdo
     */
    public function setDriver(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getDriver()
    {
        return $this->pdo;
    }
}
