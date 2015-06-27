<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:16 PM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;

/**
 * Class Congressman
 * @package Althingi\Service
 */
class Congressman implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one Congressman.
     *
     * @param int $id
     * @return object
     */
    public function get($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Congressman` C where congressman_id = :id
        ");
        $statement->execute(['id' => $id]);
        return $this->decorate($statement->fetchObject());
    }

    /**
     * Get all Assemblies.
     *
     * @param int $offset
     * @param int $size
     * @return array
     */
    public function fetchAll($offset, $size)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Congressman` C order by C.`name` asc
            limit {$offset}, {$size}
        ");
        $statement->execute();
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Create one Congressman. This method accepts object
     * from corresponding Form.
     *
     * @param $data
     * @return string
     */
    public function create($data)
    {
        $statement = $this->getDriver()->prepare($this->insertString('Congressman', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
    }

    /**
     * Update one Congressman. This method accepts object
     * from corresponding Form.
     *
     * @param $data
     * @return int Should be 1, for one entry updated.
     */
    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString('Congressman', $data, "congressman_id = {$data->congressman_id}")
        );
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    /**
     * Delete one congressman.
     *
     * @param $id
     * @return int Should be 1, for one entry deleted.
     */
    public function delete($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Congressman`
            where congressman_id = :id
        ");
        $statement->execute(['id' => $id]);
        return $statement->rowCount();
    }

    /**
     * Count all Congressmen.
     *
     * @return int
     */
    public function count()
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `Congressman` C
        ");
        $statement->execute();
        return (int) $statement->fetchColumn(0);
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

    /**
     * Decorate and convert one entry object.
     *
     * @param $object
     * @return null|object
     */
    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->congressman_id = (int) $object->congressman_id;
        return $object;
    }
}
