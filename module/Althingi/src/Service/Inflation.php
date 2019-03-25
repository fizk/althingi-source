<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use PDO;
use DateTime;

/**
 * Class Inflation
 * @package Althingi\Service
 */
class Inflation implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /** @var  \Zend\EventManager\EventManager */
    private $events;

    /**
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return \Althingi\Model\Inflation[]
     */
    public function fetchAll(?DateTime $from = null, ?DateTime $to = null): array
    {
        if ($from !== null && $to === null) {
            $statement = $this->getDriver()->prepare(
                "select * from `Inflation`
                where `date` >= :from 
                order by `date`"
            );
            $statement->execute(['from' => $from->format('Y-m-d')]);
        } elseif ($from !== null && $to !== null) {
            $statement = $this->getDriver()->prepare(
                "select * from `Inflation` 
                where `date` between :from and :to
                order by `date`"
            );
            $statement->execute([
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
            ]);
        } elseif ($from === null && $to !== null) {
            $statement = $this->getDriver()->prepare(
                "select * from `Inflation` 
                where `date` <= :to
                order by `date`"
            );
            $statement->execute(['to' => $to->format('Y-m-d')]);
        } else {
            $statement = $this->getDriver()->prepare(
                "select * from `Inflation` order by `date`"
            );
            $statement->execute();
        }

        return array_map(function ($object) {
            return (new Hydrator\Inflation)->hydrate($object, new Model\Inflation());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function get(int $id): ? Model\Inflation
    {
        $statement = $this->getDriver()->prepare("select * from `Inflation` where id = :id");
        $statement->execute(['id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object ? (new Hydrator\Inflation())
            ->hydrate($object, new Model\Inflation())
            : null;
    }

    /**
     * @param \Althingi\Model\Inflation $data
     * @return int
     */
    public function save(Model\Inflation $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Inflation', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * @param \Althingi\Model\Inflation | object $data
     * @return int
     */
    public function update(Model\Inflation $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Inflation', $data, "id={$data->getId()}")
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
