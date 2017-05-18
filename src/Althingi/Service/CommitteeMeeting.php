<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\CommitteeMeeting as CommitteeMeetingModel;
use Althingi\Hydrator\CommitteeMeeting as CommitteeMeetingHydrator;
use PDO;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class CommitteeMeeting implements DatabaseAwareInterface
{
    use DatabaseService;

    /** @var  \PDO */
    private $pdo;

    /**
     * @param $id
     * @return \Althingi\Model\CommitteeMeeting|null
     */
    public function get(int $id): ?CommitteeMeetingModel
    {
        $statement = $this->getDriver()->prepare('
            select * from `CommitteeMeeting` where committee_meeting_id = :committee_meeting_id
        ');
        $statement->execute([
            'committee_meeting_id' => $id,
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new CommitteeMeetingHydrator())->hydrate($object, new CommitteeMeetingModel())
            : null;
    }

    /**
     * @param int $assemblyId
     * @param int $committeeId
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
            return (new CommitteeMeetingHydrator())->hydrate($object, new CommitteeMeetingModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\CommitteeMeeting $data
     * @return int affected rows
     */
    public function create(CommitteeMeetingModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('CommitteeMeeting', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\CommitteeMeeting $data
     * @return int affected rows
     */
    public function update(CommitteeMeetingModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('CommitteeMeeting', $data, "committee_meeting_id={$data->getCommitteeMeetingId()}")
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
