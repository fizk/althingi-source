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
            select C.*, S.`from` as `date` from Session S
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
     * Get Constituency by congressman on a specific date.
     *
     * @param int $congressmanId
     * @param int $assemblyId
     * @return \Althingi\Model\ConstituencyDate | null
     */
    public function getByCongressmanAndConstituency(int $congressmanId, int $assemblyId): ? Model\ConstituencyDate
    {
        $statement = $this->getDriver()->prepare('
            select C.*, S.`from` as `date` from Session S
                join Constituency C on (S.constituency_id = C.constituency_id)
            where S.assembly_id = :assembly_id and S.congressman_id = :congressman_id
                group by S.constituency_id
                having min(S.`from`);
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'assembly_id' => $assemblyId,
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\ConstituencyDate())->hydrate($object, new Model\ConstituencyDate())
            : null ;
    }

    /**
     * Get Constituency by congressman on a specific assembly.
     *
     * @param int $congressmanId
     * @param int $assemblyId
     * @return \Althingi\Model\ConstituencyDate | null
     */
    public function getByAssemblyAndCongressman(int $congressmanId, int $assemblyId): ? Model\ConstituencyDate
    {
        $statement = $this->getDriver()->prepare('
            select C.*, S.`from` as `date` from Session S
                join Constituency C on (S.constituency_id = C.constituency_id)
            where S.assembly_id = :assembly_id and S.congressman_id = :congressman_id order by S.`from` limit 0, 1
            ;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'assembly_id' => $assemblyId,
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
     * Proponents of an issue (l) grouped by constituency and counted.
     *
     * @param int $assemblyId
     * @return \Althingi\Model\ConstituencyValue[]
     */
    public function fetchFrequencyByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare('
            select count(*) as `value`, C.* from (
                select DC.congressman_id, D.date from (
                    select D.* from Document D
                        join Issue I on (
                            D.issue_id = I.issue_id and 
                            D.assembly_id = I.assembly_id and 
                            D.category = I.category
                        )
                    where D.assembly_id = :assembly_id and I.type = "l"
                    group by D.issue_id
                    having min(D.date)
                ) as D
                    join Document_has_Congressman DC
                        on (D.assembly_id = DC.assembly_id and D.issue_id = DC.issue_id)
            ) as A
                join Session S
                    on A.congressman_id = S.congressman_id and (
                        (A.date between S.`from` and S.`to`) or
                        (A.date >= S.`from` and S.`to` is null)
                    )
                join Constituency C on S.constituency_id = C.constituency_id
            group by C.constituency_id;
        ');
        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($object) {
            return (new Hydrator\ConstituencyValue())->hydrate($object, new Model\ConstituencyValue());
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
