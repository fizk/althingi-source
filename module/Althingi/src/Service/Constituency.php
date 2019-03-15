<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\ConstituencyDate as ConstituencyDateModel;
use Althingi\Hydrator\ConstituencyDate as ConstituencyDateHydrator;
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
     * @return \Althingi\Model\Constituency | null
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
     * Get Constituency by congressman on a specific date.
     *
     * @param int $congressmanId
     * @param \DateTime $date
     * @return ConstituencyDateModel | null
     */
    public function getByCongressman(int $congressmanId, \DateTime $date)
    {
        $statement = $this->getDriver()->prepare('
            select C.*, S.`from` as `date` from
            (
                select * from `Session` S where
                (:date between S.`from` and S.`to`) or
                (:date >= S.`from` and S.`to` is null)
            ) S
            Join `Constituency` C on (C.constituency_id = S.constituency_id)
            where S.congressman_id = :congressman_id;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'date' => $date->format('Y-m-d'),
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new ConstituencyDateHydrator())->hydrate($object, new ConstituencyDateModel())
            : null ;
    }

    /**
     * Get all Constituencies by congressman, order by first occupied.
     *
     * @param int $congressmanId
     * @return array
     */
    public function fetchByCongressman(int $congressmanId)
    {
        $statement = $this->getDriver()->prepare('
            select C.*, S.`from` as `date` from `Session` S
                join `Constituency` C on (C.constituency_id = S.constituency_id)
            where S.congressman_id = :constituency_id
            group by C.constituency_id
            having min(S.`from`)
            order by S.`from`;
        ');
        $statement->execute(['constituency_id' => $congressmanId]);

        return array_map(function ($object) {
            return (new ConstituencyDateHydrator())->hydrate($object, new ConstituencyDateModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
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
