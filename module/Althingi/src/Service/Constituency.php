<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\Constituency as ConstituencyModel;
use Althingi\Hydrator\Constituency as ConstituencyHydrator;
use PDO;

/**
 * Class Constituency
 * @package Althingi\Service
 */
class Constituency implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @param int $id
     * @return \Althingi\Model\Constituency
     */
    public function get(int $id): ?ConstituencyModel
    {
        $statement = $this->getDriver()->prepare(
            'select * from `Constituency` 
            where constituency_id = :constituency_id'
        );
        $statement->execute(['constituency_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new ConstituencyHydrator())->hydrate($object, new ConstituencyModel())
            : null;
    }

    /**
     * Create one Constituency. Accepts object from
     * corresponding Form.
     *
     * @param \Althingi\Model\Constituency $data
     * @return int
     */
    public function create(ConstituencyModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Constituency', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\Constituency $data
     * @return int
     */
    public function save(ConstituencyModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Constituency', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * @param \Althingi\Model\Constituency $data
     * @return int
     */
    public function update(ConstituencyModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Constituency', $data, "constituency_id={$data->getConstituencyId()}")
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
