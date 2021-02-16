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
class Plenary implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @param int $assemblyId
     * @param int $plenaryId
     * @return \Althingi\Model\Plenary|null
     */
    public function get(int $assemblyId, int $plenaryId): ? Model\Plenary
    {
        $statement = $this->getDriver()->prepare('
            select * from `Plenary` where assembly_id = :assembly_id and plenary_id = :plenary_id
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'plenary_id' => $plenaryId,
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Plenary())->hydrate($object, new Model\Plenary())
            : null;
    }

    /**
     * Fetch all Plenaries from given Assembly.
     *
     * @param int $id
     * @param int $offset
     * @param int $size
     * @param string $order
     * @return \Althingi\Model\Plenary[]
     */
    public function fetchByAssembly(int $id, int $offset, int $size = null, string $order = 'desc'): array
    {
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';
        $statement = $this->getDriver()->prepare("
            select * from `Plenary` P where assembly_id = :id
            order by P.`from` {$order}
            limit {$offset}, {$size}
        ");
        $statement->execute(['id' => $id]);

        return array_map(function ($object) {
            return (new Hydrator\Plenary())->hydrate($object, new Model\Plenary());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Count all plenaries by Assembly.
     *
     * @param int $id Assembly ID
     * @return int
     */
    public function countByAssembly(int $id): int
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
     * @param \Althingi\Model\Plenary $data
     * @return string
     */
    public function create(Model\Plenary $data)
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Plenary', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\Plenary $data
     * @return string
     */
    public function save(Model\Plenary $data)
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Plenary', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * @param \Althingi\Model\Plenary | object $data
     * @return int
     */
    public function update(Model\Plenary $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'Plenary',
                $data,
                "plenary_id = {$data->getPlenaryId()} and assembly_id = {$data->getAssemblyId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }
}
