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
 * Class Plenary
 * @package Althingi\Service
 */
class Plenary implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Fetch all Plenaries from given Assembly.
     *
     * @param $id Assembly ID
     * @param $offset
     * @param $size
     * @param string $order
     * @return array
     */
    public function fetchByAssembly($id, $offset, $size, $order = 'desc')
    {
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';
        $statement = $this->getDriver()->prepare("
            select * from `Plenary` P where assembly_id = :id
            order by P.`from` {$order}
            limit {$offset}, {$size}
        ");
        $statement->execute(['id' => $id]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Count all plenaries by Assembly.
     *
     * @param $id Assembly ID
     * @return int
     */
    public function countByAssembly($id)
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `Plenary` P where assembly_id = :id
        ");
        $statement->execute(['id' => $id]);
        return (int) $statement->fetchColumn(0);
    }

    /**
     * Create one Plenary. Accepts object
     * from corresponding Form.
     *
     * @param $data
     * @return string
     */
    public function create($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->insertString('Plenary', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
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
     * Decorate and convert one entry.
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
        $object->plenary_id = (int) $object->plenary_id;
        return $object;
    }
}
