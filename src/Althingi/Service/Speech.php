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

    public function get($id)
    {
        $statement = $this->getDriver()->prepare(
            'select * from `Speech` where speech_id = :speech_id'
        );
        $statement->execute(['speech_id' => $id]);
        return $statement->fetchObject();
    }

    /**
     * @param $assemblyId
     * @param $issueId
     * @param int $offset
     * @param int $size
     * @return array
     */
    public function fetchByIssue($assemblyId, $issueId, $offset = 0, $size = 25)
    {
        $statement = $this->getDriver()->prepare("
          select *, timestampdiff(SECOND, `from`, `to`) as `time`
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

    public function fetchFrequencyByIssue($assemblyId, $issueId)
    {
        $statement = $this->getDriver()->prepare('
            select date_format(`from`, "%Y-%m") as `year_month`, (sum(timediff(`to`, `from`))/60) as `count`
            from `Speech`
            where assembly_id = :assembly_id and issue_id = :issue_id
            group by date_format(`from`, "%Y-%m")
            order by `from`;
        ');

        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId
        ]);

        return array_map(function ($speech) {
            $speech->count = (int) $speech->count;
            return $speech;
        }, $statement->fetchAll());
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

    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString('Speech', $data, "speech_id={$data->assembly_id}")
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

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->plenary_id = (int) $object->plenary_id;
        $object->assembly_id = (int) $object->assembly_id;
        $object->issue_id = (int) $object->issue_id;
        $object->congressman_id = (int) $object->congressman_id;

        return $object;
    }
}
