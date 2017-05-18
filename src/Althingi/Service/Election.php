<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\Election as ElectionModel;
use Althingi\Hydrator\Election as ElectionHydrator;
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
    public function get(int $id): ?ElectionModel
    {
        $statement = $this->getDriver()->prepare("
            select * from `Election` where election_id = :election_id
        ");
        $statement->execute(['election_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new ElectionHydrator())->hydrate($object, new ElectionModel())
            : null;
    }

    /**
     * Get one Electin by Assembly.
     *
     * @param int $assemblyId
     * @return null|\Althingi\Model\Election
     */
    public function getByAssembly(int $assemblyId): ?ElectionModel
    {
        $statement = $this->getDriver()->prepare("
            select E.* from `Election` E 
            join `Election_has_Assembly` EA on (E.`election_id` = EA.`election_id`)
            where EA.`assembly_id` = :assembly_id;
        ");
        $statement->execute(['assembly_id' => $assemblyId]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new ElectionHydrator())->hydrate($object, new ElectionModel())
            : null;
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
