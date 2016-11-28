<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/05/15
 * Time: 1:02 PM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
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
    public function get($id)
    {
        $statement = $this->getDriver()->prepare("select * from `Assembly` where assembly_id = :id");
        $statement->execute(['id' => $id]);
        $assembly = $statement->fetchObject();

        return $assembly
            ? (new \Althingi\Model\Assembly())
                ->setAssemblyId($assembly->assembly_id)
                ->setFrom($assembly->from ? new \DateTime($assembly->from) : null)
                ->setTo($assembly->to ? new \DateTime($assembly->to) : null)
            : null;
    }

    /**
     * Get all Assemblies.
     *
     * @param int $offset
     * @param int $size
     * @param string $order
     * @return array
     */
    public function fetchAll($offset = null, $size = null, $order = 'desc')
    {
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';

        $query = "select * from `Assembly` A order by A.`from` {$order}";
        $limitQuery = "select * from `Assembly` A order by A.`from` {$order} limit {$offset}, {$size}";

        $statement = $this->getDriver()->prepare(
            ($offset && $size) ? $limitQuery : $query
        );
        $statement->execute();

        return array_map(function ($assembly) {
            return (new \Althingi\Model\Assembly())
                ->setAssemblyId($assembly->assembly_id)
                ->setFrom($assembly->from ? new \DateTime($assembly->from) : null)
                ->setTo($assembly->to ? new \DateTime($assembly->to) : null);
        }, $statement->fetchAll());
    }

    /**
     * Count all assemblies.
     *
     * @return int
     */
    public function count()
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
    public function create(\Althingi\Model\Assembly $data)
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
    public function update(\Althingi\Model\Assembly $data)
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
    public function delete($id)
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
