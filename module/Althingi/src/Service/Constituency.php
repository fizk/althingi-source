<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use PDO;
use DateTime;

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
    public function get(int $id): ? Model\Constituency
    {
        $statement = $this->getDriver()->prepare(
            'select * from `Constituency` 
            where constituency_id = :constituency_id'
        );
        $statement->execute(['constituency_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Constituency())->hydrate($object, new Model\Constituency())
            : null;
    }

    /**
     * Get Constituency by congressman on a specific date.
     *
     * @param int $congressmanId
     * @param \DateTime $date
     * @return \Althingi\Model\ConstituencyDate | null
     */
    public function getByCongressman(int $congressmanId, DateTime $date): ? Model\ConstituencyDate
    {
        $statement = $this->getDriver()->prepare('
            select C.* from Session S
                join Constituency C on (S.constituency_id = C.constituency_id)
            where congressman_id = :congressman_id and (
                (:date between S.`from` and S.`to`) or
                (:date >= S.`from` and S.`to` is null)
            );
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'date' => $date->format('Y-m-d'),
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\ConstituencyDate())->hydrate($object, new Model\ConstituencyDate())
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
            return (new Hydrator\ConstituencyDate())->hydrate($object, new Model\ConstituencyDate());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Create one Constituency. Accepts object from
     * corresponding Form.
     *
     * @param \Althingi\Model\Constituency $data
     * @return int
     */
    public function create(Model\Constituency $data): int
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
    public function save(Model\Constituency $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Constituency', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * @param \Althingi\Model\Constituency | object $data
     * @return int
     */
    public function update(Model\Constituency $data): int
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
