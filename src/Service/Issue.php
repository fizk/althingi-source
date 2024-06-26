<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableIssuePresenter;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use Althingi\Model\KindEnum;
use Generator;
use InvalidArgumentException;
use PDO;

/**
 * Class Issue
 * @package Althingi\Service
 */
class Issue implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    private const ALLOWED_TYPES = [
        'a',  'f',  's',  'b',  'm',  'q',  'v',  'l', 'n', // A
        'mi', 'fh', 'dr', 'sr', 'st', 'ra', 'uu', 'þi', 'um',  // B
        'ff', 'ft', 'ko', 'ud'
    ];
    private const ALLOWED_ORDER = ['asc', 'desc'];

    public function get(int $issue_id, int $assembly_id, $kind = KindEnum::A): ?Model\Issue
    {
        $issueStatement = $this->getDriver()->prepare('
            select * from `Issue` I
            where I.`assembly_id` = :assembly_id
            and I.`issue_id` = :issue_id
            and I.`kind` = :kind
        ');
        $issueStatement->execute([
            'assembly_id' => $assembly_id,
            'issue_id' => $issue_id,
            'kind' => $kind->value
        ]);

        $object = $issueStatement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\Issue())->hydrate($object, new Model\Issue())
            : null;
    }

    /**
     * This is a Generator
     *
     * @return \Althingi\Model\Issue[] | void
     */
    public function fetchAll(array $kind = [KindEnum::A])
    {
        $statement = $this->getDriver()->prepare(
            'select * from `Issue` I where I.kind in (' .  implode(', ', array_map(function (KindEnum $item) {
                return '"' . $item->value . '"';
            }, $kind)) . ');'
        );
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            yield (new Hydrator\Issue())->hydrate($row, new Model\Issue());
        }

        $statement->closeCursor();

        return;
    }

    public function fetchAllGenerator(?int $assemblyId = null): Generator
    {
        $statement = $this->getDriver()
            ->prepare($assemblyId
                ? "select * from `Issue` where `assembly_id` = {$assemblyId}"
                : 'select * from `Issue` order by `assembly_id`');
        $statement->execute();


        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Issue())->hydrate($object, new Model\Issue());
        }
        $statement->closeCursor();
        return null;
    }

    /**
     * Get one Issue along with some metadata.
     */
    public function getWithDate(int $issue_id, int $assembly_id, $kind = KindEnum::A): ?Model\IssueAndDate
    {
        $issueStatement = $this->getDriver()->prepare(
            'select
                *,  (select D.`date` from `Document` D
                        where assembly_id = I.assembly_id and issue_id = I.issue_id and I.kind = "A"
                        order by date asc limit 0, 1)
                    as `date`
             from `Issue` I
              where I.assembly_id = :assembly_id
                and I.issue_id = :issue_id
                and I.kind = :kind'
        );
        $issueStatement->execute([
            'issue_id' => $issue_id,
            'assembly_id' => $assembly_id,
            'kind' => $kind->value
        ]);

        $object = $issueStatement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\IssueAndDate())->hydrate($object, new Model\IssueAndDate())
            : null;
    }

    public function fetchAllByAssembly(int $assembly_id)
    {
        $issueStatement = $this->getDriver()->prepare('
          select * from Issue where assembly_id = :assembly_id
        ');
        $issueStatement->execute(['assembly_id' => $assembly_id]);

        return array_map(function ($object) {
            return (new Hydrator\Issue())->hydrate($object, new Model\Issue());
        }, $issueStatement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\IssueAndDate[]
     */
    public function fetchByAssembly(
        int $assembly_id,
        int $offset,
        ?int $size,
        ?string $order = 'asc',
        array $type = [],
        array $categoryType = [],
        array $kind = [KindEnum::A]
    ): array {
        $order = in_array($order, self::ALLOWED_ORDER) ? $order : 'asc';
        $typeFilterString = $this->typeFilterString($type);
        $categoryFilterString = $this->categoryTypeFilterString($categoryType);
        $kindString = $this->categoryString($kind);
        $size = $size ? : 25;

        if (empty($categoryFilterString)) {
            $statement = $this->getDriver()->prepare("
                select I.*,
                    (
                        select D.`date` from `Document` D
                        where `assembly_id` = I.`assembly_id`
                        and `issue_id` = I.issue_id
                        and D.`kind` = I.`kind`
                        order by `date` asc limit 0, 1
                    ) as `date`
                from `Issue` I
                where I.`assembly_id` = :id
                   {$typeFilterString}
                   {$kindString}
                order by I.`issue_id` {$order}
                limit {$offset}, {$size};
            ");
        } else {
            $statement = $this->getDriver()->prepare("
                select I.*, CI.`category_id`,
                (
                    select D.`date` from `Document` D
                    where `assembly_id` = I.`assembly_id`
                    and `issue_id` = I.issue_id
                    and D.`kind` = I.kind
                    order by `date` asc limit 0, 1
                ) as `date`

                from `Issue` I
                left outer join `Category_has_Issue` CI on (
                    CI.`issue_id` = I.`issue_id`
                    and CI.`assembly_id` = I.assembly_id
                    and (CI.`kind` = I.kind or CI.`kind` is null)
                )
                where I.assembly_id = :id
                    {$typeFilterString}
                    {$kindString}
                    {$categoryFilterString}
                order by I.issue_id {$order}
                limit {$offset}, {$size};
            ");
        }

        $statement->execute(['id' => $assembly_id]);
        return array_map(function ($object) {
            return (new Hydrator\IssueAndDate())->hydrate($object, new Model\IssueAndDate());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function countByAssembly(
        int $id,
        array $type = [],
        array $categoryTypes = [],
        ?array $kind = [KindEnum::A]
    ): int {
        $typeFilterString = $this->typeFilterString($type);
        $categoryFilterString = $this->categoryTypeFilterString($categoryTypes);
        $kindString = $this->categoryString($kind);

        if (empty($categoryFilterString)) {
            $statement = $this->getDriver()->prepare("
                select count(*) from `Issue` I
                where `assembly_id` = :id {$typeFilterString} {$kindString}
            ");
        } else {
            $statement = $this->getDriver()->prepare("
                select count(*) from `Issue` I
                    join `Category_has_Issue` CI on (
                      CI.issue_id = I.issue_id and CI.assembly_id = :id {$kindString}
                    )
                where I.assembly_id = :id {$typeFilterString} {$categoryFilterString} {$kindString}
            ");
        }

        $statement->execute(['id' => $id]);
        return (int) $statement->fetchColumn(0);
    }

    /**
     * @return \Althingi\Model\Issue[]
     */
    public function fetchByCongressman(int $id): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Issue` I
              where I.`congressman_id` = :id
              and I.kind = :kind
            order by I.`assembly_id` desc, I.`issue_id` asc;
        ");

        $statement->execute([
            'id' => $id,
            'kind' => KindEnum::A->value
        ]);
        return array_map(function ($object) {
            return (new Hydrator\Issue())->hydrate($object, new Model\Issue());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * This will only return A-issue.
     *
     * @return \Althingi\Model\Issue[]
     */
    public function fetchByAssemblyAndCongressman(int $assemblyId, int $congressmanId): array
    {
        $statement = $this->getDriver()->prepare("
            select I.* from `Document_has_Congressman` D
                join `Issue` I on (I.`issue_id` = D.`issue_id` and I.assembly_id = D.assembly_id and I.kind = :kind)
                where D.assembly_id = :assembly_id
                  and D.congressman_id = :congressman_id
                  and D.`order` = 1
                order by I.`type`;
        ");

        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
            'kind' => KindEnum::A->value
        ]);
        return array_map(function ($object) {
            return (new Hydrator\Issue())->hydrate($object, new Model\Issue());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\CongressmanIssue[]
     */
    public function fetchByAssemblyAndCongressmanSummary(int $assemblyId, int $congressmanId): array
    {
        $statement = $this->getDriver()->prepare("
            select count(*) as `count`,
              DC.`order`, I.`type`, I.`type_name`, I.`type_subname`, D.`type` as `document_type`
                from `Document` D
                join `Issue` I on (
                    D.issue_id = I.issue_id
                    and D.assembly_id = I.assembly_id
                    and I.kind = :kind
                )
                join `Document_has_Congressman` DC on (
                    D.document_id = DC.document_id
                    and D.assembly_id = DC.assembly_id
                    and I.kind = :kind
                )
            where D.assembly_id = :assembly_id
              and DC.congressman_id = :congressman_id
            group by DC.`order`, I.`type`, D.`type`
            order by DC.`order`, I.`type`;
        ");

        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
            'kind' => KindEnum::A->value
        ]);
        return array_map(function ($object) {
            return (new Hydrator\CongressmanIssue())->hydrate($object, new Model\CongressmanIssue());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Count each type and status.
     *
     * Group and count `type` and `status` by assembly.
     *
     * @return \Althingi\Model\AssemblyStatus[]
     */
    public function fetchCountByCategoryAndStatus(int $assemblyId, string $kind = KindEnum::A): array
    {
        $statement = $this->getDriver()->prepare('
            select count(*) as `count`, I.`status`, I.`type`, I.`type_name`, I.`type_subname` from `Issue` I
                where I.assembly_id = :assembly_id
                 and I.kind = :kind
                group by I.`type`, I.`status` order by I.`type_name`
        ');

        $statement->execute([
            'assembly_id' => $assemblyId,
            'kind' => $kind->value,
        ]);

        return array_map(function ($object) {
            return (new Hydrator\AssemblyStatus())->hydrate($object, new Model\AssemblyStatus());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchCountByCategory(int $assemblyId)
    {
        $statement = $this->getDriver()->prepare('
            select count(*) as `count`, kind, type, type_name, type_subname
            from Issue
            where assembly_id = :assembly_id
                group by type
            order by kind, type_name;
        ');

        $statement->execute([
            'assembly_id' => $assemblyId,
        ]);

        return array_map(function ($object) {
            return (new Hydrator\AssemblyStatus())->hydrate($object, new Model\AssemblyStatus());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Count status of Government bills.
     *
     * @return \Althingi\Model\AssemblyStatus[]
     */
    public function fetchCountByGovernment(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare('
            select I.`status` as `value`, count(*) as `count` from Document D
                join Issue I on (D.assembly_id = I.assembly_id and D.issue_id = I.issue_id and I.kind = :kind)
            where D.assembly_id = :assembly_id and D.`type` = \'stjórnarfrumvarp\'
            group by I.`status`;
        ');

        $statement->execute([
            'assembly_id' => $assemblyId,
            'kind' => KindEnum::A->value
        ]);

        return array_map(function ($object) {
            return (new Hydrator\ValueAndCount())->hydrate($object, new Model\ValueAndCount());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Group and count `status` by assembly where type is `l`.
     *
     * @return \Althingi\Model\IssueTypeStatus[]
     */
    public function fetchBillStatisticsByAssembly(int $id): array
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) as `count`, I.`status` from `Issue` I
            where I.`type` = \'l\' and I.assembly_id = :assembly_id
              and I.kind = :kind
              group by `status`;'
        );

        $statement->execute([
            'assembly_id' => $id,
            'kind' => KindEnum::A->value
        ]);

        return array_map(function ($object) {
            return (new Hydrator\IssueTypeStatus())->hydrate($object, new Model\IssueTypeStatus());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\IssueTypeStatus[]
     */
    public function fetchNonGovernmentBillStatisticsByAssembly(int $id): array
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) as `count`, I.`status` from `Issue` I
            where `type` = \'l\'
              and assembly_id = :assembly_id
              and `type_subname` != \'stjórnarfrumvarp\'
              and I.kind = :kind
            group by `status`;'
        );

        $statement->execute([
            'assembly_id' => $id,
            'kind' => KindEnum::A->value
        ]);

        return array_map(function ($object) {
            return (new Hydrator\IssueTypeStatus())->hydrate($object, new Model\IssueTypeStatus());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Group and count `status` where `type_subname` is
     * `stjórnarfrumvarp`.
     *
     * @return \Althingi\Model\IssueTypeStatus[]
     */
    public function fetchGovernmentBillStatisticsByAssembly(int $id): array
    {
        $statement = $this->getDriver()->prepare('
            select count(*) as `count`, I.status from Document D
                join Issue I on (I.assembly_id = D.assembly_id and I.issue_id = D.issue_id and I.kind = D.kind)
            where D.assembly_id = :assembly_id and D.type = \'stjórnarfrumvarp\'
            group by I.status;
        ');

        $statement->execute(['assembly_id' => $id]);

        return array_map(function ($object) {
            return (new Hydrator\IssueTypeStatus())->hydrate($object, new Model\IssueTypeStatus());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Status[]
     */
    public function fetchProgress(int $assemblyId, int $issueId, string $kind = KindEnum::A): array
    {
        $statement = $this->getDriver()->prepare('
            select count(*) as value,
                D.`assembly_id`,
                D.`issue_id`,
                null as `committee_id`,
                null as `speech_id`,
                D.`document_id`,
                null as `committee_name`,
                D.`date`,
                D.`type` as `title`,
                "document" as `type`,
                true as `completed`,
                256 as `importance`
            from `Document` D
            where D.`assembly_id` = :assembly_id and D.`issue_id` = :issue_id and `kind` = :kind
            group by date_format(D.date, "%Y-%m-%d")
            having min(D.date)

            union

            select sum(TIMESTAMPDIFF(SECOND, S.`from`, S.`to`)) as value,
                S.`assembly_id`,
                S.`issue_id`,
                null as `committee_id`,
                S.`speech_id`,
                null as `document_id`,
                null as `committee_name`,
                S.`from` as `date`,
                CONCAT(S.`iteration`, ". umræða") as `title`,
                "speech" as `type`,
                true as `completed`,
                128 as `importance`
            from Speech S
            where S.assembly_id = :assembly_id and S.issue_id = :issue_id and S.`kind` = :kind
            group by date_format(S.`from`, "%Y-%m-%d")
            having min(`from`)

            union

            select count(*) as value,
                V.`assembly_id`,
                V.`issue_id`,
                null as `committee_id`,
                null as `speech_id`,
                `document_id`,
                null as `committee_name`,
                `date`,
                "atvædagreidsla" as `title`,
                "vote" as `type`,
                true as `completed`,
                64 as `importance`
            from Vote V where assembly_id = :assembly_id and issue_id = :issue_id
            group by date_format(V.`date`, "%Y-%m-%d")
            having min(`date`)

            union

            select count(*) as value,
                CMA.assembly_id,
                CMA.issue_id,
                C.committee_id,
                null as `speech_id`,
                null as `document_id`,
                C.name as `committee_name`,
                CM.`from` as `date`,
                "í nefnd" as `title`,
                "committee" as `type`,
                true as `completed`,
                256 as `importance`
            from `CommitteeMeetingAgenda` CMA
            join `CommitteeMeeting` CM on (
                CM.committee_meeting_id = CMA.committee_meeting_id and CM.`from` is not null
            )
            join `Committee` C on (CM.committee_id = C.committee_id)
            where CMA.assembly_id = :assembly_id and CMA.issue_id = :issue_id
            group by date_format(CM.`from`, "%Y-%m-%d")
            having min(CM.`from`)

            order by `date`, `importance` desc;
            ;
        ');

        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'kind' => $kind->value
        ]);

        return array_map(function ($object) {
            return (new Hydrator\Status())->hydrate($object, new Model\Status());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get all issues from an assembly plus accumulated speech times.
     *
     * @return \Althingi\Model\IssueValue[]
     */
    public function fetchByAssemblyAndSpeechTime(
        int $assemblyId,
        ?int $size = null,
        ?string $order = 'desc',
        $kind = [KindEnum::A]
    ): array {
        $limit = $size
            ? "limit 0, {$size}"
            : '';
        $statement = $this->getDriver()->prepare("
            select
                (sum(time_to_sec(timediff(`to`, `from`)))) as `value`,
                I.*
            from `Speech` S
                join `Issue` I on (
                    I.`issue_id` = S.`issue_id`
                    and I.`assembly_id` = S.`assembly_id`
                    and I.`kind` = S.`kind`
                )
            where S.`assembly_id` = :assembly and S.`kind` in (" .
                implode(', ', array_map(function (KindEnum $item) {
                    return '"' . $item->value . '"';
                }, $kind))
            . ")
                group by S.`issue_id`
                order by `value` {$order}
                {$limit}
            ;
        ");

        $statement->execute(['assembly' => $assemblyId]);

        return array_map(function ($object) {
            return (new Hydrator\IssueValue())->hydrate($object, new Model\IssueValue());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(Model\Issue $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Issue', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableIssuePresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\Issue $data): int
    {
        $statement = $this->getDriver()->prepare($this->toSaveString('Issue', $data));
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableIssuePresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableIssuePresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\Issue $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'Issue',
                $data,
                "issue_id = {$data->getIssueId()} and " .
                "assembly_id = {$data->getAssemblyId()} and " .
                "kind = '{$data->getKind()->value}'"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableIssuePresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    private function typeFilterString(array $type = []): string
    {
        if (empty($type)) {
            return '';
        }

        if (count(array_diff($type, self::ALLOWED_TYPES)) > 0) { //@todo B mál have different types
            throw new InvalidArgumentException(
                sprintf('Invalid \'type\' params %s', implode(', ', $type))
            );
        }

        return ' and I.`type` in (' . implode(
            ',',
            array_map(function ($t) {
                return "'" . $t . "'";
            }, $type)
        ) . ')';
    }

    private function categoryTypeFilterString(array $category = []): string
    {
        $category = array_filter($category, function ($item) {
            return is_numeric($item);
        });

        if (empty($category)) {
            return '';
        }

        return ' and CI.`category_id` in (' . implode(',', $category) . ')';
    }

    private function categoryString(?array $kind = [], $prefix = 'and')
    {
        if (empty($kind)) {
            return '';
        }

        $quotedCategories = array_map(function (KindEnum $item) {
            return "'" . $item->value . "'";
        }, $kind);

        return " {$prefix} I.`kind` in (" . implode(',', $quotedCategories) . ')';
    }
}
