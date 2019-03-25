<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
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
     * @param int $id
     * @return null|\Althingi\Model\Election
     */
    public function get(int $id): ? Model\Election
    {
        $statement = $this->getDriver()->prepare("
            select * from `Election` where election_id = :election_id
        ");
        $statement->execute(['election_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Election())->hydrate($object, new Model\Election())
            : null;
    }

    /**
     * Get one Election by Assembly.
     *
     * @param int $assemblyId
     * @return null|\Althingi\Model\Election
     */
    public function getByAssembly(int $assemblyId): ? Model\Election
    {
        $statement = $this->getDriver()->prepare("
            select E.* from `Election` E 
            join `Election_has_Assembly` EA on (E.`election_id` = EA.`election_id`)
            where EA.`assembly_id` = :assembly_id;
        ");
        $statement->execute(['assembly_id' => $assemblyId]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Election())->hydrate($object, new Model\Election())
            : null;
    }

    /**
     * @param \PDO $pdo
     * @return $this
     */
    public function setDriver(PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }

    /**
     * @return \PDO
     */
    public function getDriver()
    {
        return $this->pdo;
    }
}
