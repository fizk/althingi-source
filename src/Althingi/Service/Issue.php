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

/**
 * Class Issue
 * @package Althingi\Service
 */
class Issue implements DatabaseAwareInterface
{
    use DatabaseService;

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
        //ISSUE
        //  get issue
        $issueStatement = $this->getDriver()->prepare(
            "select * from `Issue`
            where issue_id = :issue_id and assembly_id = :assembly_id"
        );
        $issueStatement->execute(['issue_id'=>$issue_id, 'assembly_id'=>$assembly_id]);

        $issue = $this->decorate($issueStatement->fetchObject());

        if (!$issue) {
            return null;
        }

        //CONGRESSMEN
        //  get congressmen
        //TODO get in which party current congressman is in.
        $congressmenStatement = $this->getDriver()->prepare("
            select * from `Congressman` where congressman_id in (
                select congressman_id from `Speech`
                where assembly_id = :assembly_id and issue_id = :issue_id
                group by congressman_id
            ) order by name
        ");
        $congressmenStatement->execute(['issue_id'=>$issue_id, 'assembly_id'=>$assembly_id]);
        $issue->speakers = $congressmenStatement->fetchAll();

        //CONGRESSMAN
        //TODO Make sure that this works.
        $congressmanStatement = $this->getDriver()->prepare("
            select * from `Congressman` where congressman_id = :congressman_id;
        ");
        $congressmanStatement->execute(['congressman_id'=>$issue->congressman_id]);
        unset($issue->congressman_id);

        $issue->foreman = $congressmanStatement->fetchObject() ? : null ;

        //FIXME this doesn't always work
        $totalSpeakTimeStatement = $this->getDriver()->prepare("
            select TIME_FORMAT(sum(TIMEDIFF(time(`to`), time(`from`))), '%H:%i:%s') as the_diff
            from `Speech`where assembly_id = :assembly_id and issue_id = :issue_id
        ");
        $totalSpeakTimeStatement->execute(['issue_id'=>$issue_id, 'assembly_id'=>$assembly_id]);
        $issue->time = $totalSpeakTimeStatement->fetchColumn(0);

        return $issue;
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
     * @return array
     */
    public function fetchByAssembly($id, $offset, $size, $order = 'asc')
    {
        $order = in_array($order, ['asc', 'desc']) ? $order : 'asc';
        $statement = $this->getDriver()->prepare("
            select * from `Issue` I where assembly_id = :id
            order by I.`issue_id` {$order}
            limit {$offset}, {$size}
        ");
        $statement->execute(['id' => $id]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * Count all Issues per Assembly.
     *
     * @param int $id Assembly ID
     * @return int count
     */
    public function countByAssembly($id)
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `Issue` I where `assembly_id` = :id
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
        $object->type = (int) $object->type;
        $object->name = ucfirst($object->name);

        return $object;
    }
}
