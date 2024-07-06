<?php

namespace Althingi\Service;

use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use Althingi\Presenters\IndexableInflationPresenter;
use PDO;
use DateTime;
use Generator;

/**
 * Class Inflation
 * @package Althingi\Service
 */
class Inflation implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

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
            return (new Hydrator\Inflation())->hydrate($object, new Model\Inflation());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Inflation[]
     */
    public function fetchAllGenerator(): Generator
    {
        $statement = $this->getDriver()
            ->prepare('select * from `Inflation` order by `date`');
        $statement->execute();


        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Inflation())->hydrate($object, new Model\Inflation());
        }
        $statement->closeCursor();
        return null;
    }

    public function get(int $id): ?Model\Inflation
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

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableInflationPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableInflationPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
        }

        return $statement->rowCount();
    }

    public function update(Model\Inflation $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Inflation', $data, "id={$data->getId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableInflationPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $statement->rowCount();
    }
}
