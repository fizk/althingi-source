<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 10/06/15
 * Time: 8:53 PM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;
use InvalidArgumentException;

/**
 * Class Issue
 * @package Althingi\Service
 */
class Issue implements DatabaseAwareInterface
{
    use DatabaseService;

    const ALLOWED_TYPES = ['a', 'b', 'l', 'm', 'q', 's'];
    const ALLOWED_ORDER = ['asc', 'desc'];

    const STATUS_WAITING_ONE    = 'Bíður 1. umræðu';
    const STATUS_WAITING_TWO    = 'Bíður 2. umræðu';
    const STATUS_WAITING_THREE  = 'Bíður 3. umræðu';
    const STATUS_COMMITTEE_ONE  = 'Í nefnd eftir 1. umræðu';
    const STATUS_APPROVED       = 'Samþykkt sem lög frá Alþingi';
    const STATUS_TO_GOVERNMENT  = 'Vísað til ríkisstjórnar';

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one Issue along with some metadata.
     *
     * Issue is a combined key, so you need assembly and issue
     * number.
     *
     * @param $issue_id
     * @param $assembly_id
     * @return null|object
     */
    public function get($issue_id, $assembly_id)
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

        return $this->decorate($issueStatement->fetchObject());
    }

    /**
     * Get all Issues per Assembly.
     *
     * Result set is always restricted by size.
     *
     * @param int $id
     * @param int $offset
     * @param int $size
     * @param string $order
     * @param array $type
     * @return array
     */
    public function fetchByAssembly($id, $offset, $size, $order = 'asc', $type = [])
    {
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
        $statement->execute(['id' => $id]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Fetch all issues that a given congressman has
     * been the fourman of.
     *
     * @param $id
     * @return array
     */
    public function fetchByCongressman($id)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Issue` I where I.`congressman_id` = :id
            order by I.`assembly_id` desc, I.`issue_id` asc;
        ");

        $statement->execute(['id' => $id]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Get the state of issues by assembly.
     *
     * Group and count `type` by assembly.
     *
     * @param $assemblyId
     * @return array
     */
    public function fetchStateByAssembly($assemblyId)
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) as `count`, `type`, `type_name`, `type_subname` from `Issue`
            where assembly_id = :assembly_id group by `type` order by `type_name`;'
        );

        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($item) {
            $item->count = is_numeric($item->count) ? (int) $item->count : null;
            return $item;
        }, $statement->fetchAll());
    }

    /**
     * Group and count `status` by assembly where type is `l`.
     *
     * @param $id
     * @return array
     */
    public function fetchBillStatisticsByAssembly($id)
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) as `count`, `status` from `Issue`
            where `type` = \'l\' and assembly_id = :assembly_id group by `status`;'
        );

        $statement->execute(['assembly_id' => $id]);

        return array_map(function ($item) {
            $item->count = is_numeric($item->count) ? (int) $item->count : null;
            return $item;
        }, $statement->fetchAll());
    }

    /**
     * Group and count `status` where `type_subname` is
     * `stjórnarfrumvarp`.
     *
     * @param $id
     * @return array
     */
    public function fetchGovernmentBillStatisticsByAssembly($id)
    {
        $statement = $this->getDriver()->prepare(
            'SELECT count(*) AS `count`, `status`
            FROM `Issue`
            WHERE `type_subname` = \'stjórnarfrumvarp\' AND assembly_id = :assembly_id GROUP BY `status`;'
        );

        $statement->execute(['assembly_id' => $id]);

        return array_map(function ($item) {
            $item->count = is_numeric($item->count) ? (int) $item->count : null;
            return $item;
        }, $statement->fetchAll());
    }

    /**
     * Count all Issues per Assembly.
     *
     * @param int $id Assembly ID
     * @param array $type
     * @return int count
     */
    public function countByAssembly($id, $type)
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
     * @param object $data
     * @return string
     */
    public function create($data)
    {
        $statement = $this->getDriver()->prepare($this->insertString('Issue', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
    }

    /**
     * Update one Issue.
     *
     * @param $data
     * @return int affected rows
     */
    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString('Issue', $data, "issue_id = {$data->issue_id} and assembly_id = {$data->assembly_id}")
        );
        $statement->execute($this->convert($data));
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
     * Decorate and convert one Issue result object.
     *
     * @param $object
     * @return null|object
     */
    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->assembly_id = (int) $object->assembly_id;
        $object->issue_id = (int) $object->issue_id;
        $object->name = ucfirst(trim($object->name));
        $object->type_name = ucfirst(trim($object->type_name));
        $object->type_subname = ucfirst(trim($object->type_subname));

        return $object;
    }

    private function typeFilterString($type)
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
