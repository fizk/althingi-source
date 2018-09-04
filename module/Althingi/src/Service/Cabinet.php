<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\Cabinet as CabinetModel;
use Althingi\Hydrator\Cabinet as CabinetHydrator;
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

    /**
     * @param int $assemblyId
     * @return \Althingi\Model\Cabinet[]
     */
    public function fetchByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare(
            'select C.* from `Cabinet` C
              join `Cabinet_has_Assembly` A on (C.cabinet_id = A.cabinet_id)
              where A.assembly_id = :assembly_id;'
        );
        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($object) {
            return (new CabinetHydrator())->hydrate($object, new CabinetModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
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
