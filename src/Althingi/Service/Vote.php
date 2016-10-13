<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/03/2016
 * Time: 11:22 AM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;

class Vote implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function get($id)
    {
        $statement = $this->getDriver()->prepare('
            select * from `Vote` where vote_id = :vote_id
        ');
        $statement->execute(['vote_id' => $id]);
        return $statement->fetchObject();
    }

    public function fetchByIssue($assemblyId, $issueId)
    {
        $statement =$this->getDriver()->prepare('
            select * from `Vote` V
            where V.`issue_id` = :issue_id and V.`assembly_id` = :assembly_id
            order by V.`date` asc;
        ');
        $statement->execute([
            'issue_id' => $issueId,
            'assembly_id' => $assemblyId,
        ]);

        return $statement->fetchAll();
    }

    public function fetchDateFrequencyByIssue($assemblyId, $issueId)
    {
        $statement = $this->getDriver()->prepare('
            select count(*) as `count`, date_format(`date`, "%Y-%m") as `year_month` from `Vote`
            where assembly_id = :assembly_id and issue_id = :issue_id
            group by `year_month`
            order by `year_month`;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId
        ]);
        return array_map(function ($vote) {
            $vote->count = (int) $vote->count;
            return $vote;
        }, $statement->fetchAll());
    }

    public function fetchFrequencyByAssembly($assemblyId)
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) as `count`, date_format(`date`, "%Y-%m") as `vote_date`
            from `Vote`
            where assembly_id = :assembly_id
            group by `vote_date`;'
        );
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map(function ($vote) {
            $vote->count = (int) $vote->count;
            return $vote;
        }, $statement->fetchAll());
    }

    public function fetchByDocument($assemblyId, $issueId, $documentId)
    {
        $statement = $this->getDriver()->prepare('
            select * from `Vote`
            where assembly_id = :assembly_id and issue_id = :issue_id and document_id = :document_id
            order by `date`;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'document_id' => $documentId,
        ]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    public function getFrequencyByAssemblyAndCongressman($assemblyId, $congressmanId, \DateTime $from = null, \DateTime $to = null)
    {
        $statement;
        if ($from) {
            $to = $to ? $to : new \DateTime();
            $statement = $this->getDriver()->prepare('
                select count(*) as `count`, VI.`vote` from `Vote` V 
                join `VoteItem` VI on (V.`vote_id` = VI.`vote_id`)
                where V.`assembly_id` = :assembly_id and VI.`congressman_id` = :congressman_id  and (V.`date` between :from and :to)
                group by VI.`vote`;
            ');
            $statement->execute([
                'assembly_id' => $assemblyId,
                'congressman_id' => $congressmanId,
                'from' => $from->format('Y-m-d H:i:s'),
                'to' => $to->format('Y-m-d H:i:s'),
            ]);
        } else {
            $statement = $this->getDriver()->prepare('
                select count(*) as `count`, VI.`vote` from `Vote` V 
                join `VoteItem` VI on (V.`vote_id` = VI.`vote_id`)
                where V.`assembly_id` = :assembly_id and VI.`congressman_id` = :congressman_id
                group by VI.`vote`;
            ');
            $statement->execute([
                'assembly_id' => $assemblyId,
                'congressman_id' => $congressmanId,
            ]);
        }

        return array_map(function ($type) {
            $type->count = (int) $type->count;
            return $type;
        }, $statement->fetchAll());
    }

    public function countByAssembly($assemblyId)
    {
        $statement = $this->getDriver()->prepare('
            select count(*) from `Vote` V where V.`assembly_id` = :assembly_id;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
        ]);
        return (int) $statement->fetchColumn(0);
    }

    public function create($data)
    {
        $insertStatement = $this->getDriver()->prepare($this->insertString('Vote', $data));
        $insertStatement->execute($this->convert($data));
    }

    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString('Vote', $data, "vote_id = {$data->vote_id}")
        );
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->vote_id = (int) $object->vote_id;
        $object->issue_id = (int) $object->issue_id;
        $object->assembly_id = (int) $object->assembly_id;
        $object->document_id = (int) $object->document_id;
        $object->yes = (int) $object->yes;
        $object->no = (int) $object->no;
        $object->inaction = (int) $object->inaction;

        return $object;
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
}
