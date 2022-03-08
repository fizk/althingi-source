<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use PDO;
use DateTime;
use Generator;

/**
 * Class Inflation
 * @package Althingi\Service
 */
class Inflation implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
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

    public function fetchAllGenerator(): Generator
    {
        $statement = $this->getDriver()
            ->prepare('select * from `Inflation` order by `date`');
        $statement->execute();


        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Inflation)->hydrate($object, new Model\Inflation());
        }
        $statement->closeCursor();
        return null;
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

    public function save(Model\Inflation $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Inflation', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    public function update(Model\Inflation $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Inflation', $data, "id={$data->getId()}")
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }
}
