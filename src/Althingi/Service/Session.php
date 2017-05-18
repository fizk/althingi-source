<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\Session as SessionModel;
use Althingi\Hydrator\Session as SessionHydrator;
use PDO;
use DateTime;

/**
 * Class Session
 * @package Althingi\Service
 */
class Session implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one Congressman's Session.
     *
     * @param int $id
     * @return null|\Althingi\Model\Session
     */
    public function get(int $id): ?SessionModel
    {
        $statement = $this->getDriver()->prepare(
            "select * from `Session` where session_id = :session_id"
        );
        $statement->execute(['session_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new SessionHydrator())->hydrate($object, new SessionModel())
            : null;
    }

    /**
     * Fetch all Session by Congressman.
     *
     * @param int $id
     * @return \Althingi\Model\Session[]
     */
    public function fetchByCongressman(int $id): array
    {
        $statement =$this->getDriver()->prepare("
            select * from `Session` where congressman_id = :id
            order by `from` desc
        ");
        $statement->execute(['id' => $id]);

        return array_map(function ($object) {
            return (new SessionHydrator())->hydrate($object, new SessionModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
     * @return array
     */
    public function fetchByAssemblyAndCongressman(int $assemblyId, int $congressmanId): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Session` S where S.`congressman_id` = :congressman_id and S.`assembly_id` = :assembly_id 
            order by `from` desc
        ");

        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);
        return array_map(function ($object) {
            return (new SessionHydrator())->hydrate($object, new SessionModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $congressmanId
     * @param DateTime $from
     * @param string $type
     * @return int
     */
    public function getIdentifier(int $congressmanId, DateTime $from, string $type): int
    {
        $statement = $this->getDriver()->prepare('
            select `session_id` from `Session`
            where `congressman_id` = :congressman_id and `type` = :type and `from` = :from;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'type' => $type,
            'from' => $from->format('Y-m-d'),
        ]);
        return $statement->fetchColumn(0);
    }

    /**
     * Create one entry. Accepts object from
     * corresponding Form.
     *
     * @param \Althingi\Model\Session $data
     * @return int affected rows
     */
    public function create(SessionModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Session', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * Update one Congressman's Session. Accepts object from
     * corresponding Form.
     *
     * @param \Althingi\Model\Session $data
     * @return int
     */
    public function update(SessionModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Session', $data, "session_id={$data->getSessionId()}")
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * Delete one Congressman's session.
     *
     * @param int $id
     * @return int
     */
    public function delete(int $id)
    {
        $statement = $this->getDriver()->prepare("
            delete from `Session` where session_id = :id
        ");
        $statement->execute(['id' => $id]);
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
