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
     * @return null|object
     */
    public function get($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Assembly` where assembly_id = :id
        ");
        $statement->execute(['id' => $id]);

        $assembly = $statement->fetchObject();
        return $this->decorate($assembly);
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
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Count all assemblies.
     *
     * @return int
     */
    public function count()
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `Assembly` A
        ");
        $statement->execute();
        return (int) $statement->fetchColumn(0);
    }

    /**
     * Create one entry.
     *
     * @param object $data
     * @return int affected rows
     */
    public function create($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->insertString('Assembly', $data));
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    /**
     * Update one entry.
     *
     * @param object $data
     * @return int affected rows
     */
    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString('Assembly', $data, "assembly_id={$data->assembly_id}")
        );
        $statement->execute($this->convert($data));
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
        $statement = $this
            ->getDriver()
            ->prepare(
                "delete from `Assembly` where assembly_id = :assembly_id"
            );
        $statement->execute(['assembly_id' => $id]);
        return $statement->rowCount();
    }

    /**
     * Decorate and convert one Assembly result object.
     *
     * @param $object
     * @return null|object
     */
    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->assembly_id = (int) $object->assembly_id;

        return $object;
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
