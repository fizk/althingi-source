<?php

namespace Althingi\Service;

use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use PDO;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class CommitteeMeetingAgenda implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @param $meetingId
     * @param $agendaId
     * @return \Althingi\Model\CommitteeMeetingAgenda|null
     */
    public function get(int $meetingId, int $agendaId): ? Model\CommitteeMeetingAgenda
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
            ? (new Hydrator\CommitteeMeetingAgenda())->hydrate($object, new Model\CommitteeMeetingAgenda())
            : null;
    }

    public function fetch(int $meetingId): array
    {
        $statement = $this->getDriver()->prepare('
            select * from `CommitteeMeetingAgenda` C
            where C.`committee_meeting_id` = :committee_meeting_id;
        ');
        $statement->execute([
            'committee_meeting_id' => $meetingId,
        ]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($object) {
            return (new Hydrator\CommitteeMeetingAgenda())->hydrate($object, new Model\CommitteeMeetingAgenda());
        }, $result);
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\CommitteeMeetingAgenda $data
     * @return int affected rows
     */
    public function create(Model\CommitteeMeetingAgenda $data): int
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
    public function save(Model\CommitteeMeetingAgenda $data): int
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
     * @param \Althingi\Model\CommitteeMeetingAgenda | object $data
     * @return int affected rows
     */
    public function update(Model\CommitteeMeetingAgenda $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'CommitteeMeetingAgenda',
                $data,
                "committee_meeting_id={$data->getCommitteeMeetingId()} " .
                "and committee_meeting_agenda_id={$data->getCommitteeMeetingAgendaId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }
}
