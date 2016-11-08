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
class Election implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one Election.
     *
     * @param $id
     * @return null|object
     */
    public function get($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Election` where election_id = :election_id
        ");
        $statement->execute(['election_id' => $id]);

        $assembly = $statement->fetchObject();
        return $this->decorate($assembly);
    }

    /**
     * Get one Electin by Assembly.
     *
     * @param $assemblyId
     * @return null|object
     */
    public function getByAssembly($assemblyId)
    {
        $statement = $this->getDriver()->prepare("
            select E.* from `Election` E 
            join `Election_has_Assembly` EA on (E.`election_id` = EA.`election_id`)
            where EA.`assembly_id` = :assembly_id;
        ");
        $statement->execute(['assembly_id' => $assemblyId]);

        $assembly = $statement->fetchObject();
        return $this->decorate($assembly);
    }

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->election_id = (int) $object->election_id;

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
