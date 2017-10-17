<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\Assembly as AssemblyModel;
use Althingi\Hydrator\Assembly as AssemblyHydrator;
use PDO;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class Assembly implements DatabaseAwareInterface
{
    use DatabaseService;

    const ALLOWED_TYPES = ['a', 'b', 'l', 'm', 'q', 's'];

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one Assembly.
     *
     * @param $id
     * @return null|\Althingi\Model\Assembly
     */
    public function get(int $id): ?AssemblyModel
    {
        $statement = $this->getDriver()->prepare("select * from `Assembly` where assembly_id = :id");
        $statement->execute(['id' => $id]);
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new AssemblyHydrator)->hydrate($assembly, new AssemblyModel())
            : null;
    }

    public function getCurrent()
    {
        $statement = $this->getDriver()->prepare("select * from `Assembly` order by `assembly_id` desc limit 0, 1");
        $statement->execute();
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new AssemblyHydrator)->hydrate($assembly, new AssemblyModel())
            : null;
    }

    /**
     * Get all Assemblies.
     *
     * @param int $offset
     * @param int $size
     * @param string $order
     * @return \Althingi\Model\Assembly[]
     */
    public function fetchAll(int $offset = null, int $size = null, string $order = 'desc'): array
    {
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';

        $query = "select * from `Assembly` A order by A.`from` {$order}";
        $limitQuery = "select * from `Assembly` A order by A.`from` {$order} limit {$offset}, {$size}";

        $statement = $this->getDriver()->prepare(
            ($offset && $size) ? $limitQuery : $query
        );
        $statement->execute();

        return array_map(function ($assembly) {
            return (new AssemblyHydrator)->hydrate($assembly, new AssemblyModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Count all assemblies.
     *
     * @return int
     */
    public function count(): int
    {
        $statement = $this->getDriver()->prepare("select count(*) from `Assembly` A");
        $statement->execute();

        return (int) $statement->fetchColumn(0);
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\Assembly $data
     * @return int affected rows
     */
    public function create(AssemblyModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Assembly', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * Update one entry.
     *
     * @param \Althingi\Model\Assembly $data
     * @return int affected rows
     */
    public function update(AssemblyModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Assembly', $data, "assembly_id={$data->getAssemblyId()}")
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * Delete one Assembly.
     * Should return 1, for one assembly deleted.
     *
     * @param int $id
     * @return int
     */
    public function delete(int $id): int
    {
        $statement = $this->getDriver()->prepare("delete from `Assembly` where assembly_id = :assembly_id");
        $statement->execute(['assembly_id' => $id]);

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
