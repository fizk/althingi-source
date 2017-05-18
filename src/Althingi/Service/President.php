<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\President as PresidentModel;
use Althingi\Model\PresidentCongressman as PresidentCongressmanModel;
use Althingi\Hydrator\President as PresidentHydrator;
use Althingi\Hydrator\PresidentCongressman as PresidentCongressmanHydrator;
use PDO;
use DateTime;

/**
 * Class President
 * @package Althingi\Service
 */
class President implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function get(int $id): ?PresidentModel
    {
        $statement = $this->getDriver()->prepare(
            "select * 
                from `President` P 
                where P.`president_id` = :president_id;"
        );
        $statement->execute(['president_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new PresidentHydrator())->hydrate($object, new PresidentModel())
            : null;
    }

    /**
     * @param int $id
     * @return \Althingi\Model\PresidentCongressman|null
     */
    public function getWithCongressman(int $id): ?PresidentCongressmanModel
    {
        $statement = $this->getDriver()->prepare(
            "select P.`president_id`, P.`assembly_id`, P.`from`, P.`to`, P.`title`, P.`abbr`, C.* 
                from `President` P 
                join `Congressman` C on (P.`congressman_id` = C.`congressman_id`)
                where P.`president_id` = :president_id;"
        );
        $statement->execute(['president_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new PresidentCongressmanHydrator())->hydrate($object, new PresidentCongressmanModel())
            : null;
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
     * @param DateTime $from
     * @param string $title
     * @return \Althingi\Model\PresidentCongressman|null
     */
    public function getByUnique(
        int $assemblyId,
        int $congressmanId,
        DateTime $from,
        string $title
    ): ?PresidentCongressmanModel {
        $statement = $this->getDriver()->prepare("
            select P.`president_id`, P.`assembly_id`, P.`from`, P.`to`, P.`title`, P.`abbr`, C.* 
            from `President` P 
            join `Congressman` C on (P.`congressman_id` = C.`congressman_id`)
            where P.`assembly_id` = :assembly_id 
              and P.`congressman_id` = :congressman_id 
              and P.`title` = :title 
              and P.`from` = :from;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
            'title' => $title,
            'from' => $from->format('Y-m-d'),
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new PresidentCongressmanHydrator())->hydrate($object, new PresidentCongressmanModel())
            : null;
    }

    /**
     * @param \Althingi\Model\President $data
     * @return int
     */
    public function create(PresidentModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('President', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\President $data
     * @return int
     */
    public function update(PresidentModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('President', $data, "president_id={$data->getPresidentId()}")
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
}
