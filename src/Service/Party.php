<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexablePartyPresenter;
use Althingi\Injector\{DatabaseAwareInterface, EventsAwareInterface};
use PDO;
use DateTime;

class Party implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $id): ? Model\Party
    {
        $statement = $this->getDriver()->prepare('
            select * from `Party` where party_id = :party_id
        ');
        $statement->execute(['party_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Party())->hydrate($object, new Model\Party())
            : null;
    }

    /**
     * @return \Althingi\Model\Party[]
     */
    public function fetch(): array
    {
        $statement = $this->getDriver()->prepare('
            select * from `Party` order by `name`
        ');
        $statement->execute();

        return array_map(function ($object) {
            return (new Hydrator\Party())->hydrate($object, new Model\Party());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getByCongressman(int $congressmanId, DateTime $date): ? Model\Party
    {
        $statement = $this->getDriver()->prepare('
            select P.* from Session S
                join Party P on S.party_id = P.party_id
            where congressman_id = :congressman_id and (
                (:date between S.`from` and S.`to`) or
                (:date >= S.`from` and S.`to` is null)
            );
        ');

        $statement->execute([
            'congressman_id' => $congressmanId,
            'date' => $date->format('Y-m-d')
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Party())->hydrate($object, new Model\Party())
            : null;
    }

    public function getByCongressmanAndAssembly(int $congressmanId, int $assemblyId): ? Model\Party
    {
        $statement = $this->getDriver()->prepare('
            select P.*, S.`from` as `date` from Session S
                join Party P on (S.party_id = P.party_id)
            where S.assembly_id = :assembly_id and S.congressman_id = :congressman_id
                group by S.constituency_id
                having min(S.`from`);
        ');

        $statement->execute([
            'congressman_id' => $congressmanId,
            'assembly_id' => $assemblyId
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Party())->hydrate($object, new Model\Party())
            : null;
    }

    /**
     * @return \Althingi\Model\PartyAndTime[]
     */
    public function fetchTimeByAssembly(int $assemblyId, array $category = ['A']): array
    {
        $categories = count($category) > 0
            ? 'and SP.category in (' . implode(',', array_map(function ($c) {
                return '"' . $c . '"';
            }, $category)) . ')'
            : '';

        $statement = $this->getDriver()->prepare("
            select sum(T.`time_sum`) as `total_time`, P.* from (
                select
                    SP.`congressman_id`,
                    TIME_TO_SEC(timediff(SP.`to`, SP.`from`)) as `time_sum`,
                    SE.party_id
                from `Speech` SP
                join `Session` SE ON (
                  SE.`congressman_id` = SP.`congressman_id`
                  and ((SP.`from` between SE.`from` and SE.`to`) or (SP.`from` >= SE.`from` and SE.`to` is null))
                )
                where SP.`assembly_id` = :assembly_id {$categories}

            ) as T
            join `Party` P on (P.`party_id` = T.`party_id`)
            group by T.`party_id`
            order by `total_time`, P.`party_id` desc;
        ");
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map(function ($object) {
            return (new Hydrator\PartyAndTime())->hydrate($object, new Model\PartyAndTime());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Party[]
     */
    public function fetchByCongressmanAndAssembly(int $congressmanId, int $assemblyId): array
    {

        $statement = $this->getDriver()->prepare('
            select P.* from Session S
                join Party P on (S.party_id = P.party_id)
                where congressman_id = :congressman_id and assembly_id = :assembly_id
                group by S.party_id
                order by S.`from`;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'assembly_id' => $assemblyId,
        ]);
        return array_map(function ($object) {
            return (new Hydrator\Party())->hydrate($object, new Model\Party());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Party[]
     */
    public function fetchByAssembly(int $assemblyId, array $exclude = []): array
    {
        $query = '';
        if (count($exclude) == 0) {
            $query = '
                select P.* from `Session` S
                join `Party` P on (P.`party_id` = S.`party_id`)
                where S.`assembly_id` = :assembly_id
                group by S.`party_id`;
            ';
        } else {
            $query = '
                select P.* from `Session` S
                join `Party` P on (P.`party_id` = S.`party_id`)
                where S.`assembly_id` = :assembly_id and P.`party_id` not in ('.implode(',', $exclude).')
                group by S.`party_id`;
            ';
        }

        $statement = $this->getDriver()->prepare($query);
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map(function ($object) {
            return (new Hydrator\Party())->hydrate($object, new Model\Party());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\PartyAndElection[]
     */
    public function fetchElectedByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare('
            select * from `ElectionResult` ER join `Election_has_Assembly` E on (E.`election_id` = ER.`election_id`)
            join `Party` P on (P.party_id = ER.party_id)
            where E.`assembly_id` = :assembly_id order by ER.`result` desc;
        ');
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map(function ($object) {
            return (new Hydrator\PartyAndElection())->hydrate($object, new Model\PartyAndElection());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Party[]
     */
    public function fetchByCongressman(int $congressmanId): array
    {
        $statement = $this->getDriver()->prepare(
            'select P.* from `Session` S
            join `Party` P on (P.party_id = S.party_id)
            where congressman_id = :congressman_id group by `party_id`;'
        );
        $statement->execute(['congressman_id' => $congressmanId]);
        return array_map(function ($object) {
            return (new Hydrator\Party())->hydrate($object, new Model\Party());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Party[]
     */
    public function fetchByCabinet(int $cabinetId): array
    {
        $statement = $this->getDriver()->prepare('
            select P.* from `Cabinet_has_Congressman` CC
            join `Session` SE ON
              (
                SE.`congressman_id` = CC.`congressman_id` and ((CC.`from` between SE.`from` and SE.`to`) or
                (CC.`from` >= SE.`from` and SE.`to` is null))
              )
            join `Party` P on (SE.`party_id` = P.`party_id`)
            where cabinet_id = :cabinet_id
            group by SE.`party_id`;
        ');
        $statement->execute(['cabinet_id' => $cabinetId]);
        return array_map(function ($object) {
            return (new Hydrator\Party())->hydrate($object, new Model\Party());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(Model\Party $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Party', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexablePartyPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\Party $data): int
    {
        $statement = $this->getDriver()->prepare($this->toSaveString('Party', $data));
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexablePartyPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexablePartyPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\Party $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Party', $data, "party_id={$data->getPartyId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexablePartyPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }
}
