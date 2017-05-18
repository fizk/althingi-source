<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\Committee as CommitteeModel;
use Althingi\Hydrator\Committee as CommitteeHydrator;
use PDO;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class Committee implements DatabaseAwareInterface
{
    use DatabaseService;

    /** @var  \PDO */
    private $pdo;

    /**
     * @param $id
     * @return \Althingi\Model\Committee|null
     */
    public function get(int $id): ?CommitteeModel
    {
        $statement = $this->getDriver()->prepare('select * from `Committee` C where C.`committee_id` = :committee_id;');
        $statement->execute(['committee_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new CommitteeHydrator())->hydrate($object, new CommitteeModel())
            : null;
    }

    /**
     * @return \Althingi\Model\Committee[]
     */
    public function fetchAll(): array
    {
        $statement = $this->getDriver()->prepare('select * from `Committee` C order by C.`name`;');
        $statement->execute();

        return array_map(function ($object) {
            return $object
                ? (new CommitteeHydrator())->hydrate($object, new CommitteeModel())
                : null;
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param $assemblyId
     * @return \Althingi\Model\Committee[]
     */
    public function fetchByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare('
            select * from `Committee` C
              where C.`first_assembly_id` <= :assembly_id 
              and (C.`last_assembly_id` >= :assembly_id or C.`last_assembly_id` is null)
              order by C.`name`;
        ');

        $statement->execute([
            'assembly_id' => $assemblyId
        ]);

        return array_map(function ($object) {
            return $object
                ? (new CommitteeHydrator())->hydrate($object, new CommitteeModel())
                : null;
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\Committee $data
     * @return int affected rows
     */
    public function create(CommitteeModel $data): int
    {
        $statement = $this->getDriver()->prepare($this->toInsertString('Committee', $data));
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\Committee $data
     * @return int
     */
    public function update(CommitteeModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Committee', $data, "committee_id={$data->getCommitteeId()}")
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
