<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\Issue as IssueModel;
use Althingi\Hydrator\Issue as IssueHydrator;
use Althingi\Model\IssueAndDate as IssueAndDateModel;
use Althingi\Hydrator\IssueAndDate as IssueAndDateHydrator;
use Althingi\Model\AssemblyStatus as AssemblyStatusModel;
use Althingi\Hydrator\AssemblyStatus as AssemblyStatusHydrator;
use Althingi\Model\IssueTypeStatus as IssueTypeStatusModel;
use Althingi\Hydrator\IssueTypeStatus as IssueTypeStatusHydrator;
use PDO;
use InvalidArgumentException;

/**
 * Class Issue
 * @package Althingi\Service
 */
class Issue implements DatabaseAwareInterface
{
    use DatabaseService;

    const ALLOWED_TYPES = ['a', 'b', 'f', 'l', 'm', 'n', 'q', 's', 'v'];
    const ALLOWED_ORDER = ['asc', 'desc'];

    const STATUS_WAITING_ONE    = 'Bíður 1. umræðu';
    const STATUS_WAITING_TWO    = 'Bíður 2. umræðu';
    const STATUS_WAITING_THREE  = 'Bíður 3. umræðu';
    const STATUS_COMMITTEE_ONE  = 'Í nefnd eftir 1. umræðu';
    const STATUS_APPROVED       = 'Samþykkt sem lög frá Alþingi';
    const STATUS_TO_GOVERNMENT  = 'Vísað til ríkisstjórnar';

    /** @var \PDO */
    private $pdo;

    /**
     * Get one Issue along with some metadata.
     *
     * Issue is a combined key, so you need assembly and issue
     * number.
     *
     * @param $issue_id
     * @param $assembly_id
     * @return null|\Althingi\Model\Issue
     */
    public function get(int $issue_id, int $assembly_id): ?IssueModel
    {
        $issueStatement = $this->getDriver()->prepare(
            'select * from `Issue` I 
              where I.assembly_id = :assembly_id and I.issue_id = :issue_id'
        );
        $issueStatement->execute(['issue_id'=>$issue_id, 'assembly_id'=>$assembly_id]);

        $object = $issueStatement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new IssueHydrator())->hydrate($object, new IssueModel())
            : null;
    }

    /**
     * Get one Issue along with some metadata.
     *
     * Issue is a combined key, so you need assembly and issue
     * number.
     *
     * @param $issue_id
     * @param $assembly_id
     * @return null|\Althingi\Model\IssueAndDate
     */
    public function getWithDate(int $issue_id, int $assembly_id): ?IssueAndDateModel
    {
        $issueStatement = $this->getDriver()->prepare(
            'select
                *,  (select D.`date` from `Document` D
                        where assembly_id = I.assembly_id and issue_id = I.issue_id
                        order by date asc limit 0, 1)
                    as `date`
             from `Issue` I where I.assembly_id = :assembly_id and I.issue_id = :issue_id'
        );
        $issueStatement->execute(['issue_id'=>$issue_id, 'assembly_id'=>$assembly_id]);

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
     * @return \Althingi\Model\Issue[]
     */
    public function fetchByAssembly(
        int $assembly_id,
        int $offset,
        int $size,
        ?string $order = 'asc',
        array $type = []
    ): array {
        $order = in_array($order, self::ALLOWED_ORDER) ? $order : 'asc';
        $typeFilterString = $this->typeFilterString($type);

        $statement = $this->getDriver()->prepare("
            select
                *,
                (select D.`date` from `Document` D
                where assembly_id = I.assembly_id and issue_id = I.issue_id
                order by `date` asc limit 0, 1) as `date`
            from `Issue` I where assembly_id = :id {$typeFilterString}
            order by I.`issue_id` {$order}
            limit {$offset}, {$size}
        ");
        $statement->execute(['id' => $assembly_id]);
        return array_map(function ($object) {
            return (new IssueAndDateHydrator())->hydrate($object, new IssueAndDateModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
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
            select * from `Issue` I where I.`congressman_id` = :id
            order by I.`assembly_id` desc, I.`issue_id` asc;
        ");

        $statement->execute(['id' => $id]);
        return array_map(function ($object) {
            return (new IssueHydrator())->hydrate($object, new IssueModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
     * @return \Althingi\Model\Issue[]
     */
    public function fetchByAssemblyAndCongressman(int $assemblyId, int $congressmanId): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Issue` I where I.`congressman_id` = :congressman_id and I.`assembly_id` = :assembly_id
            order by I.`assembly_id` desc, I.`issue_id` asc;
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
            'select count(*) as `count`, `type`, `type_name`, `type_subname` from `Issue`
            where assembly_id = :assembly_id group by `type` order by `type_name`;'
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
            'select count(*) as `count`, `status` from `Issue`
            where `type` = \'l\' and assembly_id = :assembly_id group by `status`;'
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
            'select count(*) as `count`, `status` from `Issue`
            where `type` = \'l\' and assembly_id = :assembly_id and `type_subname` != \'stjórnarfrumvarp\' group by `status`;'
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
            'SELECT count(*) AS `count`, `status`
            FROM `Issue`
            WHERE `type_subname` = \'stjórnarfrumvarp\' AND assembly_id = :assembly_id GROUP BY `status`;'
        );

        $statement->execute(['assembly_id' => $id]);

        return array_map(function ($object) {
            return (new IssueTypeStatusHydrator())->hydrate($object, new IssueTypeStatusModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Count all Issues per Assembly.
     *
     * @param int $id Assembly ID
     * @param array $type
     * @return int count
     */
    public function countByAssembly(int $id, array $type = []): int
    {
        $typeFilterString = $this->typeFilterString($type);
        $statement = $this->getDriver()->prepare("
            select count(*) from `Issue` I
            where `assembly_id` = :id {$typeFilterString}
        ");
        $statement->execute(['id' => $id]);
        return (int) $statement->fetchColumn(0);
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

        return $this->getDriver()->lastInsertId();
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

        return $statement->rowCount();
    }

    /**
     * @param \PDO $pdo
     */
    public function setDriver(PDO $pdo)
    {
        $this->pdo = $pdo;
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

        if (count(array_diff($type, self::ALLOWED_TYPES)) > 0) {
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
}
