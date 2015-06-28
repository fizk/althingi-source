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
 * Class Speech
 * @package Althingi\Service
 */
class Speech implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function fetchByIssue($assemblyId, $issueId, $offset = 0, $size = 25)
    {
        $statement = $this->getDriver()->prepare("
          select *, date_format(timediff(`to`, `from`), '%H:%i:%s') as `time`
          from `Speech`
          where assembly_id = :assembly_id and issue_id = :issue_id
          order by `from`
          limit {$offset}, {$size};
        ");
        $statement->execute(['assembly_id' => $assemblyId, 'issue_id' => $issueId]);

        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    public function countByIssue($assemblyId, $issueId)
    {
        $statement = $this->getDriver()->prepare("
          select count(*) from `Speech`
          where assembly_id = :assembly_id and issue_id = :issue_id
        ");
        $statement->execute(['assembly_id' => $assemblyId, 'issue_id' => $issueId]);
        return $statement->fetchColumn(0);
    }

    /**
     * Create one Speech. Accepts object from
     * corresponding Form.
     *
     * @param $data
     * @return int
     */
    public function create($data)
    {
        $statement = $this->getDriver()->prepare($this->insertString('Speech', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
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

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        //GET CONGRESSMAN
        //  get congressman
        $congressmanStatement = $this->getDriver()->prepare("
            select * from `Congressman` where congressman_id = :id
        ");
        $congressmanStatement->execute(['id' => $object->congressman_id]);
        $object->congressman = $congressmanStatement->fetchObject();
        unset($object->congressman_id);


        return $object;
    }
}
