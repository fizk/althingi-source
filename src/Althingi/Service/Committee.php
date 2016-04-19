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
class Committee implements DatabaseAwareInterface
{
    use DatabaseService;

    /** @var  \PDO */
    private $pdo;

    public function get($id)
    {
        $statement = $this->getDriver()->prepare('
            select * from `Committee` C where C.`committee_id` = :committee_id;
        ');
        $statement->execute([
            'committee_id' => $id
        ]);
        return $this->decorate($statement->fetchObject());
    }

    public function fetchAll()
    {
        $statement = $this->getDriver()->prepare('
            select * from `Committee` C order by C.`name`;
        ');
        $statement->execute();
        return array_map([$this, 'decorate'], $statement->fetchAll());
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
            ->prepare($this->insertString('Committee', $data));
        $statement->execute($this->convert($data));
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

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->committee_id = (int) $object->committee_id;
        $object->first_assembly_id = $object->first_assembly_id ? (int) $object->first_assembly_id : null;
        $object->last_assembly_id = $object->last_assembly_id ? (int) $object->last_assembly_id : null;

        return $object;
    }
}
