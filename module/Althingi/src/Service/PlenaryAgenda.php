<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\PlenaryAgenda as PlenaryAgendaModel;
use Althingi\Hydrator\PlenaryAgenda as PlenaryAgendaHydrator;
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
     * @return PlenaryAgendaModel|null
     */
    public function get(int $assemblyId, int $plenaryId, int $itemId)
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
            ? (new PlenaryAgendaHydrator)->hydrate($assembly, new PlenaryAgendaModel())
            : null;
    }

    public function fetch(int $assemblyId, int $plenaryId)
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
            return (new PlenaryAgendaHydrator)->hydrate($item, new PlenaryAgendaModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param \Althingi\Model\PlenaryAgenda $data
     * @return string
     */
    public function create(PlenaryAgendaModel $data)
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
    public function save(PlenaryAgendaModel $data)
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
