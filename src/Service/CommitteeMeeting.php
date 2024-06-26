<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use PDO;

class CommitteeMeeting implements DatabaseAwareInterface
{
    use DatabaseService;

    public function get(int $id): ?Model\CommitteeMeeting
    {
        $statement = $this->getDriver()->prepare('
            select * from `CommitteeMeeting` where committee_meeting_id = :committee_meeting_id
        ');
        $statement->execute([
            'committee_meeting_id' => $id,
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\CommitteeMeeting())->hydrate($object, new Model\CommitteeMeeting())
            : null;
    }

    /**
     * @return \Althingi\Model\CommitteeMeeting[]
     */
    public function fetchByAssembly(int $assemblyId, int $committeeId): array
    {
        $statement = $this->getDriver()->prepare('
            select * from `CommitteeMeeting` C where assembly_id = :assembly_id and committee_id = :committee_id
            order by C.`from`
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'committee_id' => $committeeId,
        ]);

        return array_map(function ($object) {
            return (new Hydrator\CommitteeMeeting())->hydrate($object, new Model\CommitteeMeeting());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(Model\CommitteeMeeting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('CommitteeMeeting', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\CommitteeMeeting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('CommitteeMeeting', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    public function update(Model\CommitteeMeeting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('CommitteeMeeting', $data, "committee_meeting_id={$data->getCommitteeMeetingId()}")
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }
}
