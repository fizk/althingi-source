<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Lib\EventsAwareInterface;
use Althingi\Model\CongressmanIssue as CongressmanIssueModel;
use Althingi\Hydrator\CongressmanIssue as CongressmanIssueHydrator;
use Althingi\Model\Issue as IssueModel;
use Althingi\Hydrator\Issue as IssueHydrator;
use Althingi\Model\IssueAndDate as IssueAndDateModel;
use Althingi\Hydrator\IssueAndDate as IssueAndDateHydrator;
use Althingi\Model\AssemblyStatus as AssemblyStatusModel;
use Althingi\Hydrator\AssemblyStatus as AssemblyStatusHydrator;
use Althingi\Model\IssueTypeStatus as IssueTypeStatusModel;
use Althingi\Hydrator\IssueTypeStatus as IssueTypeStatusHydrator;
use Althingi\Presenters\IndexableIssuePresenter;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Hydrator\IssueValue as IssueValueHydrator;
use Althingi\Model\IssueValue as IssueValueModel;
use InvalidArgumentException;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use PDO;

/**
 * Class Issue
 * @package Althingi\Service
 */
class Issue implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;

    const ALLOWED_TYPES = ['a', 'b', 'f', 'l', 'm', 'n', 'q', 's', 'v', 'ft', 'um'];
    const ALLOWED_ORDER = ['asc', 'desc'];
    const MAX_ROW_COUNT = '18446744073709551615';

    const STATUS_WAITING_ONE    = 'Bíður 1. umræðu';
    const STATUS_WAITING_TWO    = 'Bíður 2. umræðu';
    const STATUS_WAITING_THREE  = 'Bíður 3. umræðu';
    const STATUS_COMMITTEE_ONE  = 'Í nefnd eftir 1. umræðu';
    const STATUS_APPROVED       = 'Samþykkt sem lög frá Alþingi';
    const STATUS_TO_GOVERNMENT  = 'Vísað til ríkisstjórnar';

    /** @var \PDO */
    private $pdo;

    /** @var \Zend\EventManager\EventManagerInterface */
    protected $events;

    /**
     * Get one Issue along with some metadata.
     *
     * Issue is a combined key, so you need assembly and issue
     * number.
     *
     * @param $issue_id
     * @param $assembly_id
     * @param $category
     * @return null|\Althingi\Model\Issue
     */
    public function get(int $issue_id, int $assembly_id, $category = 'A'): ?IssueModel
    {
        $issueStatement = $this->getDriver()->prepare(
            'select * from `Issue` I 
              where I.assembly_id = :assembly_id 
              and I.issue_id = :issue_id
              and I.category = :category'
        );
        $issueStatement->execute([
            'issue_id' => $issue_id,
            'assembly_id' => $assembly_id,
            'category' => $category
        ]);

        $object = $issueStatement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new IssueHydrator())->hydrate($object, new IssueModel())
            : null;
    }

    /**
     * This is a Generator
     * @param $category
     * @return \Althingi\Model\Issue[]
     */
    public function fetchAll(array $category = ['A'])
    {
        $statement = $this->getDriver()->prepare(
            'select * from `Issue` I where I.category in (' .  implode(', ', array_map(function ($c) {
                return '"' . $c . '"';
            }, $category)) . ');'
        );
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            yield (new IssueHydrator())->hydrate($row, new IssueModel());
        }

        $statement->closeCursor();

        return;
    }

    /**
     * Get one Issue along with some metadata.
     *
     * Issue is a combined key, so you need assembly and issue
     * number.
     *
     * @param $issue_id
     * @param $assembly_id
     * @param $category
     * @return null|\Althingi\Model\IssueAndDate
     */
    public function getWithDate(int $issue_id, int $assembly_id, ?string $category = 'A'): ?IssueAndDateModel
    {
        $issueStatement = $this->getDriver()->prepare(
            'select
                *,  (select D.`date` from `Document` D
                        where assembly_id = I.assembly_id and issue_id = I.issue_id and I.category = "A"
                        order by date asc limit 0, 1)
                    as `date`
             from `Issue` I 
              where I.assembly_id = :assembly_id 
                and I.issue_id = :issue_id
                and I.category = :category'
        );
        $issueStatement->execute(['issue_id' => $issue_id, 'assembly_id' => $assembly_id, 'category' => $category]);

        $object = $issueStatement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new IssueAndDateHydrator())->hydrate($object, new IssueAndDateModel())
            : null;
    }

    /**
     * Get all Issues per Assembly.
     *
     * Result set is always restricted by size.
     *
     * @param int $assembly_id
     * @param int $offset
     * @param int $size
     * @param string $order
     * @param array $type
     * @param array $categoryType
     * @param array $category
     * @return \Althingi\Model\IssueAndDate[]
     */
    public function fetchByAssembly(
        int $assembly_id,
        int $offset,
        ?int $size,
        ?string $order = 'asc',
        array $type = [],
        array $categoryType = [],
        array $category = ['A']
    ): array {
        $order = in_array($order, self::ALLOWED_ORDER) ? $order : 'asc';
        $typeFilterString = $this->typeFilterString($type);
        $categoryFilterString = $this->categoryTypeFilterString($categoryType);
        $categoryString = $this->categoryString($category);
        $size = $size ? : 25;

        if (empty($categoryFilterString)) {
            $statement = $this->getDriver()->prepare("
                select I.*,
                    (
                        select D.`date` from `Document` D
                        where `assembly_id` = I.`assembly_id` 
                        and `issue_id` = I.issue_id 
                        and D.`category` = I.`category`
                        order by `date` asc limit 0, 1
                    ) as `date`
                from `Issue` I 
                where I.`assembly_id` = :id
                   {$typeFilterString}
                   {$categoryString}
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
                    and D.`category` = I.category
                    order by `date` asc limit 0, 1
                ) as `date`
                
                from `Issue` I
                left outer join `Category_has_Issue` CI on (
                    CI.`issue_id` = I.`issue_id` 
                    and CI.`assembly_id` = I.assembly_id
                    and (CI.`category` = I.category or CI.`category` is null)
                )
                where I.assembly_id = :id
                    {$typeFilterString}
                    {$categoryString}
                    {$categoryFilterString}
                order by I.issue_id {$order}
                limit {$offset}, {$size};
            ");
        }

        $statement->execute(['id' => $assembly_id]);
        return array_map(function ($object) {
            return (new IssueAndDateHydrator())->hydrate($object, new IssueAndDateModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }


    /**
     * Count all Issues per Assembly.
     *
     * @param int $id Assembly ID
     * @param array $type
     * @param array $categoryTypes
     * @param array $category
     * @return int count
     */
    public function countByAssembly(int $id, array $type = [], array $categoryTypes = [], ?array $category = ['A']): int
    {
        $typeFilterString = $this->typeFilterString($type);
        $categoryFilterString = $this->categoryTypeFilterString($categoryTypes);
        $categoryString = $this->categoryString($category);

        if (empty($categoryFilterString)) {
            $statement = $this->getDriver()->prepare("
                select count(*) from `Issue` I 
                where `assembly_id` = :id {$typeFilterString} {$categoryString}
            ");
        } else {
            $statement = $this->getDriver()->prepare("
                select count(*) from `Issue` I
                    join `Category_has_Issue` CI on (
                      CI.issue_id = I.issue_id and CI.assembly_id = :id {$categoryString}
                    )
                where I.assembly_id = :id {$typeFilterString} {$categoryFilterString} {$categoryString}
            ");
        }

        $statement->execute(['id' => $id]);
        return (int) $statement->fetchColumn(0);
    }

    /**
     * Fetch all issues that a given congressman has
     * been the fourman of.
     *
     * @param $id
     * @return \Althingi\Model\Issue[]
     */
    public function fetchByCongressman(int $id): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Issue` I 
              where I.`congressman_id` = :id
              and I.category = 'A'
            order by I.`assembly_id` desc, I.`issue_id` asc;
        ");

        $statement->execute(['id' => $id]);
        return array_map(function ($object) {
            return (new IssueHydrator())->hydrate($object, new IssueModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * This will only return A-issue.
     *
     * @param int $assemblyId
     * @param int $congressmanId
     * @return \Althingi\Model\Issue[]
     */
    public function fetchByAssemblyAndCongressman(int $assemblyId, int $congressmanId): array
    {
        $statement = $this->getDriver()->prepare("
            select I.* from `Document_has_Congressman` D
                join `Issue` I on (I.`issue_id` = D.`issue_id` and I.assembly_id = D.assembly_id and I.category = 'A')
                where D.assembly_id = :assembly_id 
                  and D.congressman_id = :congressman_id 
                  and D.`order` = 1
                order by I.`type`;
        ");

        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);
        return array_map(function ($object) {
            return (new IssueHydrator())->hydrate($object, new IssueModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
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
                    and I.category = 'A'
                )
                join `Document_has_Congressman` DC on (
                    D.document_id = DC.document_id 
                    and D.assembly_id = DC.assembly_id 
                    and I.category = 'A'
                )
            where D.assembly_id = :assembly_id 
              and DC.congressman_id = :congressman_id
            group by DC.`order`, I.`type`, D.`type`
            order by DC.`order`, I.`type`;
        ");

        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);
        return array_map(function ($object) {
            return (new CongressmanIssueHydrator())->hydrate($object, new CongressmanIssueModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get the state of issues by assembly.
     *
     * Group and count `type` by assembly.
     *
     * @param int $assemblyId
     * @return \Althingi\Model\AssemblyStatus[]
     */
    public function fetchStateByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) as `count`, I.`type`, I.`type_name`, I.`type_subname` from `Issue` I
            where I.assembly_id = :assembly_id
             and I.category = \'A\'
            group by I.`type` order by I.`type_name`;'
        );

        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($object) {
            return (new AssemblyStatusHydrator())->hydrate($object, new AssemblyStatusModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Group and count `status` by assembly where type is `l`.
     *
     * @param $id
     * @return \Althingi\Model\IssueTypeStatus[]
     */
    public function fetchBillStatisticsByAssembly(int $id): array
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) as `count`, I.`status` from `Issue` I
            where I.`type` = \'l\' and I.assembly_id = :assembly_id
              and I.category = \'A\'
              group by `status`;'
        );

        $statement->execute(['assembly_id' => $id]);

        return array_map(function ($object) {
            return (new IssueTypeStatusHydrator())->hydrate($object, new IssueTypeStatusModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param $id
     * @return \Althingi\Model\IssueTypeStatus[]
     */
    public function fetchNonGovernmentBillStatisticsByAssembly(int $id): array
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) as `count`, I.`status` from `Issue` I
            where `type` = \'l\' 
              and assembly_id = :assembly_id 
              and `type_subname` != \'stjórnarfrumvarp\' 
              and I.category = \'A\'
            group by `status`;'
        );

        $statement->execute(['assembly_id' => $id]);

        return array_map(function ($object) {
            return (new IssueTypeStatusHydrator())->hydrate($object, new IssueTypeStatusModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Group and count `status` where `type_subname` is
     * `stjórnarfrumvarp`.
     *
     * @param $id
     * @return \Althingi\Model\IssueTypeStatus[]
     */
    public function fetchGovernmentBillStatisticsByAssembly(int $id): array
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) AS `count`, I.`status` from `Issue` I
            where I.`type_subname` = \'stjórnarfrumvarp\' 
              and I.assembly_id = :assembly_id
             and I.category = \'A\'
            group by I.`status`;'
        );

        $statement->execute(['assembly_id' => $id]);

        return array_map(function ($object) {
            return (new IssueTypeStatusHydrator())->hydrate($object, new IssueTypeStatusModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param $issueId
     * @return \Althingi\Model\Status[]
     */
    public function fetchProgress(int $assemblyId, $issueId): array
    {
        $statement = $this->getDriver()->prepare('
            select D.`assembly_id`, 
                    D.`issue_id`, 
                    null as `committee_id`, 
                    null as `speech_id`, 
                    D.`document_id`,
                    null as `committee_name`,
                    D.`date`, 
                    D.`type` as `title`,
                    \'document\' as `type`,
                    true as `completed`
                from `Document` D 
                where D.`assembly_id` = :assembly and D.`issue_id` = :issue
                union
            select B.`assembly_id`, 
                    B.`issue_id`, 
                    null as `committee_id`, 
                    B.`speech_id`, 
                    null as `document_id`, 
                    null as `committee_name`,
                    B.`date`, 
                    B.`type` as `title`, 
                    \'speech\' as `type`,
                    true as `completed`
                    from (
                        select S.`assembly_id`, 
                                S.`issue_id`, 
                                null as `committee_id`, 
                                S.speech_id, null, 
                                S.`from` as `date`, 
                                CONCAT(S.`iteration`, \'. umræða\') as `type`, 
                                S.`iteration` 
                        from `Speech` S 
                        where assembly_id = :assembly and issue_id = :issue
                        order by S.`from`
                ) as B
                group by B.`iteration`
                union
            select CMA.assembly_id, 
                    CMA.issue_id, 
                    C.committee_id, 
                    null as `speech_id`, 
                    null as `document_id`, 
                    C.name as `committee_name`,
                    CM.`from`, 
                    \'í nefnd\' as `title`, 
                    \'committee\' as `type`,
                    true as `completed`
                from `CommitteeMeetingAgenda` CMA
                join `CommitteeMeeting` CM on (
                  CM.committee_meeting_id = CMA.committee_meeting_id and CM.`from` is not null
                )
                join `Committee` C on (CM.committee_id = C.committee_id)
                where CMA.assembly_id = :assembly and CMA.issue_id = :issue
            order by `date`;
        ');

        $statement->execute(['assembly' => $assemblyId, 'issue' => $issueId]);

        return array_map(function ($object) {
            return (new \Althingi\Hydrator\Status())->hydrate($object, new \Althingi\Model\Status());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get all issues from an assembly plus accumulated speech times.
     *
     * @param int $assemblyId
     * @param int|null $size
     * @param null|string $order
     * @param array $categories
     * @return \Althingi\Model\IssueValue[]
     */
    public function fetchByAssemblyAndSpeechTime(
        int $assemblyId,
        ?int $size = null,
        ?string $order = 'desc',
        $categories = ['A']
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
                    and I.`category` = S.`category`
                )
            where S.`assembly_id` = :assembly and S.`category` in (" .
                implode(', ', array_map(function ($c) {
                    return '"' . $c . '"';
                }, $categories))
            . ")
                group by S.`issue_id`
                order by `value` {$order}
                {$limit}
            ;
        ");

        $statement->execute(['assembly' => $assemblyId]);

        return array_map(function ($object) {
            return (new IssueValueHydrator())->hydrate($object, new IssueValueModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Create new Issue. This method
     * accepts object from corresponding Form.
     *
     * @param \Althingi\Model\Issue $data
     * @return int
     */
    public function create(IssueModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Issue', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(AddEvent::class, new AddEvent(new IndexableIssuePresenter($data)));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\Issue $data
     * @return int
     */
    public function save(IssueModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Issue', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(AddEvent::class, new AddEvent(new IndexableIssuePresenter($data)));

        return $statement->rowCount();
    }

    /**
     * Update one Issue.
     *
     * @param \Althingi\Model\Issue $data
     * @return int affected rows
     */
    public function update(IssueModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'Issue',
                $data,
                "issue_id = {$data->getIssueId()} and assembly_id = {$data->getAssemblyId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(UpdateEvent::class, new UpdateEvent(new IndexableIssuePresenter($data)));

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

    /**
     * @param array $type
     * @return string
     */
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

        return ' and I.`type` in (' .implode(
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

    private function categoryString(?array $categories = [], $prefix = 'and')
    {
        $formattedCategories = array_map(function ($c) {
            return strtoupper($c);
        }, $categories ? : []);

        $filteredCategories = array_filter($formattedCategories, function ($c) {
            return $c === 'A' || $c === 'B';
        });

        if (empty($filteredCategories)) {
            return '';
        }

        $quotedCategories = array_map(function ($c) {
            return "'" . $c . "'";
        }, $filteredCategories);

        return " {$prefix} I.`category` in (" . implode(',', $quotedCategories) . ')';
    }

    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;
        return $this;
    }

    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
