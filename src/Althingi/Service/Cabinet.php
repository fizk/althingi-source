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
class Cabinet implements DatabaseAwareInterface
{
    use DatabaseService;

    /** @var  \PDO */
    private $pdo;

    public function fetchByAssembly($assemblyId)
    {
        $statement = $this->getDriver()->prepare(
            'select C.* from `Cabinet` C
            join `Cabinet_has_Assembly` A on (C.cabinet_id = A.cabinet_id)
            where A.assembly_id = :assembly_id;'
        );
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
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
        };

        $object->cabinet_id = (int) $object->cabinet_id;
        return $object;
    }
}
