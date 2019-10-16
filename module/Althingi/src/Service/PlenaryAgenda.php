<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use PDO;

/**
 * Class Plenary
 * @package Althingi\Service
 */
class PlenaryAgenda implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @param int $assemblyId
     * @param int $plenaryId
     * @param int $itemId
     * @return \Althingi\Model\PlenaryAgenda | null
     */
    public function get(int $assemblyId, int $plenaryId, int $itemId): ? Model\PlenaryAgenda
    {
        $statement = $this->getDriver()->prepare("
          select * from `PlenaryAgenda` 
            where `assembly_id` = :assembly_id
            and `plenary_id` = :plenary_id
            and `item_id` = :item_id;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'plenary_id' => $plenaryId,
            'item_id' => $itemId,
        ]);
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new Hydrator\PlenaryAgenda)->hydrate($assembly, new Model\PlenaryAgenda())
            : null;
    }

    /**
     * @param int $assemblyId
     * @param int $plenaryId
     * @return Model\PlenaryAgenda[]
     */
    public function fetch(int $assemblyId, int $plenaryId): ? array
    {
        $statement = $this->getDriver()->prepare("
          select * from PlenaryAgenda PA 
            where PA.`assembly_id` = :assembly_id 
            and PA.`plenary_id` = :plenary_id 
            order by PA.`item_id`;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'plenary_id' => $plenaryId,
        ]);

        return array_map(function ($item) {
            return (new Hydrator\PlenaryAgenda)->hydrate($item, new Model\PlenaryAgenda());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param \Althingi\Model\PlenaryAgenda $data
     * @return string
     */
    public function create(Model\PlenaryAgenda $data)
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('PlenaryAgenda', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\PlenaryAgenda $data
     * @return string
     */
    public function save(Model\PlenaryAgenda $data)
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('PlenaryAgenda', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
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
