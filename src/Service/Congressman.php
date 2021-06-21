<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Presenters\IndexableCongressmanPresenter;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use Althingi\Events\{UpdateEvent, AddEvent};
use PDO;
use DateTime;
use Generator;

class Congressman implements DatabaseAwareInterface, EventsAwareInterface
{
    const CONGRESSMAN_TYPE_MP = 'parliamentarian';
    const CONGRESSMAN_TYPE_SUBSTITUTE = 'substitute';
    const CONGRESSMAN_TYPE_WITH_SUBSTITUTE = 'with-substitute';

    use DatabaseService;
    use EventService;

    public function get(int $id): ? Model\Congressman
    {
        $statement = $this->getDriver()->prepare("select * from `Congressman` C where congressman_id = :id");
        $statement->execute(['id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\Congressman())->hydrate($object, new Model\Congressman())
            : null ;
    }

    /**
     * @return \Althingi\Model\CongressmanAndParty[]
     */
    public function fetchAll(): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Congressman` C order by C.`name` asc
        ");
        $statement->execute();
        return array_map(function ($object) {
            return (new Hydrator\Congressman())->hydrate($object, new Model\Congressman());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchAllGenerator(?int $assemblyId = null): Generator
    {
        if ($assemblyId) {
            $statement = $this->getDriver()
                ->prepare('
                    select distinct(C.congressman_id), C.*
                    from Congressman C
                    join Session S on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id
                    order by `name`;
                ');
            $statement->execute([
                'assembly_id' => $assemblyId
            ]);
        } else {
            $statement = $this->getDriver()
                ->prepare('
                    select * from `Congressman`
                    order by `name`;
                ');
            $statement->execute();
        }

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Congressman)->hydrate($object, new Model\Congressman());
        }
        $statement->closeCursor();
        return null;
    }

    /**
     * @return \Althingi\Model\CongressmanAndParty[]
     * @deprecated
     */
    public function fetchByAssembly(int $assemblyId, string $congressmanType = null): array
    {
        switch ($congressmanType) {
            case self::CONGRESSMAN_TYPE_MP:
                $statement = $this->getDriver()->prepare(
                    'select C.*, S.party_id from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id and S.`type` = \'þingmaður\'
                    group by S.congressman_id order by S.party_id, C.name;'
                );
                break;
            case self::CONGRESSMAN_TYPE_SUBSTITUTE:
                $statement = $this->getDriver()->prepare(
                    'select C.*, S.party_id from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id and S.`type` = \'varamaður\'
                    group by S.congressman_id order by S.party_id, C.name;'
                );
                break;
            case self::CONGRESSMAN_TYPE_WITH_SUBSTITUTE:
                //TODO do I need this?
                return [];
                break;
            default:
                $statement = $this->getDriver()->prepare(
                    'select C.*, S.party_id from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                    where S.assembly_id = :assembly_id
                    group by S.congressman_id order by S.party_id, C.name;'
                );
                break;
        }
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map(function ($object) {
            return (new Hydrator\CongressmanAndParty())->hydrate($object, new Model\CongressmanAndParty());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Accumulated speech-time per congressman for a given assembly.
     *
     * @return \Althingi\Model\CongressmanValue[]
     * @deprecated
     */
    public function fetchTimeByAssembly(
        int $assemblyId,
        ?int $size = null,
        ?string $order = 'desc',
        ?array $category = ['A']
    ): array {
        $limit = $size
            ? "limit 0, {$size}"
            : '';

        $categories = count($category) > 0
            ? 'and S.category in (' . implode(', ', array_map(function ($c) {
                return '"' . $c . '"';
            }, $category)) . ')'
            : '';

        $statement = $this->getDriver()->prepare(
            "select C.*,
                (
                    select (sum(time_to_sec(timediff(`to`, `from`)))) as `count`
                    from `Speech` S
                    where S.`assembly_id` = :assembly_id and S.`congressman_id` = C.congressman_id {$categories}
                    group by `congressman_id`
                ) as `value`
                from `Session` S
                    join `Congressman` C on (C.congressman_id = S.congressman_id)
                where S.assembly_id = :assembly_id and S.`type` = 'þingmaður'
                group by S.congressman_id order by `value` {$order} {$limit};"
        );
        $statement->execute([
            'assembly_id' => $assemblyId,
        ]);

        return array_map(function ($speech) {
            return (new Hydrator\CongressmanValue())->hydrate($speech, new Model\CongressmanValue());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Accumulates submitted types of issue per congressman.
     *
     * @return \Althingi\Model\CongressmanValue[]
     * @deprecated
     */
    public function fetchIssueTypeCountByAssembly(
        int $assemblyId,
        ?int $size,
        $type = [],
        ?string $order = 'desc'
    ): array {
        $limit = $size
            ? "limit 0, {$size}"
            : '';
        $types = count($type) > 0
            ? "where I.`type` in (" . implode(', ', array_map(function ($i) {
                return "'{$i}'";
            }, $type)) . ")"
            : '';

        $statement = $this->getDriver()->prepare("
            select count(*) as `value`, A.congressman_id, I.type, C.* from (
                select DhC.* from Document D
                    join Document_has_Congressman DhC on (
                        D.document_id = DhC.document_id and
                        D.issue_id = DhC.issue_id and
                        D.assembly_id = DhC.assembly_id and
                        D.category = DhC.category
                    )
                    where D.assembly_id = :assembly_id
                group by D.issue_id
                having min(D.date)
            ) as A
                join Issue I on (
                    A.issue_id = I.issue_id and
                    A.assembly_id = I.assembly_id and
                    A.category = I.category
                )
                join Congressman C on (A.congressman_id = C.congressman_id)
                {$types}
            group by A.congressman_id order by `value` {$order} {$limit}
        ");

        $statement->execute([
            'assembly_id' => $assemblyId,
        ]);

        return array_map(function ($speech) {
            return (new Hydrator\CongressmanValue())->hydrate($speech, new Model\CongressmanValue());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\CongressmanAndCabinet[]
     * @deprecated
     */
    public function fetchByCabinet(int $cabinetId): array
    {
        $statement = $this->getDriver()->prepare(
            'select C.*, CC.`title`, CC.`from` as `date` from `Cabinet_has_Congressman` CC
            join `Congressman` C on (CC.congressman_id = C.`congressman_id`)
            where CC.`cabinet_id` = :cabinet_id order by C.`name`;'
        );
        $statement->execute(['cabinet_id' => $cabinetId]);
        return array_map(function ($object) {
            return (new Hydrator\CongressmanAndCabinet())->hydrate($object, new Model\CongressmanAndCabinet());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\CongressmanAndDateRange[]
     * @deprecated
     */
    public function fetchAccumulatedTimeByIssue(int $assemblyId, int $issueId, ?string $category = 'A'): array
    {
        $statement = $this->getDriver()->prepare("
            select C.*, (sum(`diff`)) as `time`, date(`from`) as `begin`, null as `end` from (
                select *, timediff(`to`, `from`) as `diff`
                from `Speech` D
                where D.assembly_id = :assembly_id and D.issue_id = :issue_id and D.category = :category
            ) S
            join `Congressman` C on (C.congressman_id = S.congressman_id)
            group by S.congressman_id
            order by `time` desc;
        ");
        $statement->execute([
            'issue_id' => $issueId,
            'assembly_id' => $assemblyId,
            'category' => $category
        ]);

        return array_map(function ($object) {
            return (new Hydrator\CongressmanAndRange())->hydrate($object, new Model\CongressmanAndDateRange());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Proponent[]
     * @deprecated
     */
    public function fetchProponents(int $assemblyId, int $documentId): array
    {
        $statement = $this->getDriver()->prepare(
            'select C.*, D.`minister` from `Document_has_Congressman` D
            join `Congressman` C on (C.congressman_id = D.congressman_id)
            where assembly_id = :assembly_id and document_id = :document_id
            order by D.`order` asc;'
        );
        $statement->execute([
            'assembly_id' => $assemblyId,
            'document_id' => $documentId
        ]);

        return array_map(function ($object) {
            return (new Hydrator\Proponent())->hydrate($object, new Model\Proponent());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Proponent[]
     * @deprecated
     */
    public function fetchProponentsByIssue(int $assemblyId, int $issueId): array
    {
        $statement = $this->getDriver()->prepare('
            select C.*, DC.`minister`, DC.`order` from `Document_has_Congressman` DC
                join `Congressman` C on (C.`congressman_id` = DC.`congressman_id`)
            where DC.`issue_id` = :issue_id
                and DC.`assembly_id` = :assembly_id
                and DC.`document_id` = (
                    select D.`document_id` from `Document` D
                    where D.`assembly_id` = :assembly_id
                        and D.`issue_id` = :issue_id
                        and D.`category` = \'A\'
                    order by `date` asc limit 0, 1
                )
                order by DC.`order`;
        ');

        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId
        ]);

        return array_map(function ($object) {
            return (new Hydrator\Proponent())->hydrate($object, new Model\Proponent());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @deprecated
     */
    public function getAverageAgeByAssembly(int $assemblyId, DateTime $date): float
    {
        $statement = $this->getDriver()->prepare('
            select avg(TIMESTAMPDIFF(YEAR, C.birth, :date)) AS age
            from Congressman C where congressman_id in (
                select distinct congressman_id from Session where assembly_id = :assembly_id and type = \'þingmaður\'
            );
        ');

        $statement->execute([
            'assembly_id' => $assemblyId,
            'date' => $date->format('Y-m-d')
        ]);

        return $statement->fetchColumn(0);
    }

    /**
     * @return \Althingi\Model\President[]
     * @deprecated
     */
    public function fetchPresidents(): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `President` P
            join `Congressman` C on (C.`congressman_id` = P.`congressman_id`);
        ");
        $statement->execute();

        return array_map(function ($object) {
            return (new Hydrator\President())->hydrate($object, new Model\President());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @deprecated
     */
    public function getPresidentByAssembly(int $assemblyId, int $presidentId): ?Model\President
    {
        $statement = $this->getDriver()->prepare("
            select * from `President` P
            join `Congressman` C on (C.`congressman_id` = P.`congressman_id`)
            where P.`assembly_id` = :assembly_id
            and P.`president_id` = :president_id;
        ");
        $statement->execute(['assembly_id' => $assemblyId, 'president_id' => $presidentId]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\President())->hydrate($object, new Model\President())
            : null;
    }

    /**
     * @return \Althingi\Model\President[]
     * @deprecated
     */
    public function fetchPresidentsByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `President` P
            join `Congressman` C on (C.`congressman_id` = P.`congressman_id`)
            where P.`assembly_id` = :assembly_id;
        ");
        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($object) {
            return (new Hydrator\President())->hydrate($object, new Model\President());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(Model\Congressman $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Congressman', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableCongressmanPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\Congressman $data): int
    {
        $statement = $this->getDriver()->prepare($this->toSaveString('Congressman', $data));
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableCongressmanPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableCongressmanPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\Congressman $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Congressman', $data, "congressman_id={$data->getCongressmanId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableCongressmanPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $statement->rowCount();
    }

    public function delete(int $id): int
    {
        $statement = $this->getDriver()->prepare("
            delete from `Congressman`
            where congressman_id = :id
        ");
        $statement->execute(['id' => $id]);
        return $statement->rowCount();
    }

    public function count(): int
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `Congressman` C
        ");
        $statement->execute();
        return (int) $statement->fetchColumn(0);
    }
}
