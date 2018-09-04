<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;
use Althingi\Model\Vote as VoteModel;
use Althingi\Model\DateAndCount as DateAndCountModel;
use Althingi\Model\VoteTypeAndCount as VoteTypeAndCountModel;
use Althingi\Hydrator\Vote as VoteHydrator;
use Althingi\Hydrator\DateAndCount as DateAndCountHydrator;
use Althingi\Hydrator\VoteTypeAndCount as VoteTypeAndCountHydrator;

class Vote implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @param int $id
     * @return \Althingi\Model\Vote|null
     */
    public function get(int $id): ?VoteModel
    {
        $statement = $this->getDriver()->prepare('
            select * from `Vote` where vote_id = :vote_id
        ');
        $statement->execute(['vote_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new VoteHydrator())->hydrate($object, new VoteModel())
            : null ;
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @return \Althingi\Model\Vote[]
     */
    public function fetchByIssue(int $assemblyId, int $issueId): array
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

        return array_map(function ($object) {
            return (new VoteHydrator())->hydrate($object, new VoteModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function countByIssue(int $assemblyId, int $issueId): int
    {
        $statement =$this->getDriver()->prepare('
            select count(*) from `Vote` V
            where V.`issue_id` = :issue_id and V.`assembly_id` = :assembly_id;
        ');
        $statement->execute([
            'issue_id' => $issueId,
            'assembly_id' => $assemblyId,
        ]);
        return $statement->fetchColumn(0);
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @return \Althingi\Model\DateAndCount[]
     */
    public function fetchDateFrequencyByIssue(int $assemblyId, int $issueId): array
    {
        $statement = $this->getDriver()->prepare('
            select count(*) as `count`, date_format(`date`, "%Y-%m-%d") as `date` from `Vote`
            where assembly_id = :assembly_id and issue_id = :issue_id
            group by `date`
            order by `date`;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId
        ]);
        return array_map(function ($vote) {
            return (new DateAndCountHydrator())->hydrate($vote, new DateAndCountModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @return \Althingi\Model\DateAndCount[]
     */
    public function fetchFrequencyByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare(
            'select count(*) as `count`, date_format(`date`, "%Y-%m-%d") as `date`
            from `Vote`
            where assembly_id = :assembly_id
            group by `date`
            order by `date`;'
        );
        $statement->execute(['assembly_id' => $assemblyId]);
        return array_map(function ($vote) {
            return (new DateAndCountHydrator())->hydrate($vote, new DateAndCountModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param int $documentId
     * @return \Althingi\Model\Vote[]
     */
    public function fetchByDocument(int $assemblyId, int $issueId, int $documentId): array
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
        return array_map(function ($object) {
            return (new VoteHydrator())->hydrate($object, new VoteModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @todo wtf??
     * @param $assemblyId
     * @param $congressmanId
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return \Althingi\Model\VoteTypeAndCount[]
     */
    public function getFrequencyByAssemblyAndCongressman(
        int $assemblyId,
        int $congressmanId,
        \DateTime $from = null,
        \DateTime $to = null
    ): array {
        $statement = null;
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

        return array_map(function ($object) {
            return (new VoteTypeAndCountHydrator())->hydrate($object, new VoteTypeAndCountModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @return int
     */
    public function countByAssembly(int $assemblyId): int
    {
        $statement = $this->getDriver()->prepare('
            select count(*) from `Vote` V where V.`assembly_id` = :assembly_id;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
        ]);
        return (int) $statement->fetchColumn(0);
    }

    /**
     * @param VoteModel $data
     * @return int
     */
    public function create(VoteModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Vote', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param VoteModel $data
     * @return int
     */
    public function save(VoteModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Vote', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * @param VoteModel $data
     * @return int
     */
    public function update(VoteModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Vote', $data, "vote_id={$data->getVoteId()}")
        );
        $statement->execute($this->toSqlValues($data));

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
}
