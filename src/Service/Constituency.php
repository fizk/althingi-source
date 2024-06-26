<?php

namespace Althingi\Service;

use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\{DatabaseAwareInterface, EventsAwareInterface};
use Althingi\Presenters\IndexableConstituencyPresenter;
use PDO;
use DateTime;
use Generator;

class Constituency implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

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

    public function fetchAllGenerator(): Generator
    {
        $statement = $this->getDriver()
            ->prepare('select * from `Constituency` order by `constituency_id`');
        $statement->execute();


        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Constituency)->hydrate($object, new Model\Constituency());
        }
        $statement->closeCursor();
        return null;
    }

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
     * @deprecated
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
     * @return Althingi\Model\ConstituencyDate[]
     * @deprecated
     */
    public function fetchByCongressman(int $congressmanId): array
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
     * @return \Althingi\Model\ConstituencyValue[]
     * @deprecated
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
                            D.kind = I.kind
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

    public function create(Model\Constituency $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Constituency', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableConstituencyPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\Constituency $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Constituency', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableConstituencyPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableConstituencyPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
        }

        return $statement->rowCount();
    }

    public function update(Model\Constituency $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Constituency', $data, "constituency_id={$data->getConstituencyId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableConstituencyPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $statement->rowCount();
    }
}
