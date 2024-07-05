<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableCabinetPresenter;
use PDO;
use DateTime;
use Generator;

class Cabinet implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    /**
     * @return \Althingi\Model\Cabinet[]
     */
    public function fetchAll(?DateTime $from = null, ?DateTime $to = null): array
    {
        if ($from !== null && $to === null) {
            $statement = $this->getDriver()->prepare(
                "select * from `Cabinet`
                where `from` <= :from
                order by `from`"
            );
            $statement->execute(['from' => $from->format('Y-m-d')]);
        } elseif ($from !== null && $to !== null) {
            $statement = $this->getDriver()->prepare(
                "select * from `Cabinet`
                where `from` <= :from and `to` >= :to
                order by `from`"
            );
            $statement->execute([
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
            ]);
        } elseif ($from === null && $to !== null) {
            $statement = $this->getDriver()->prepare(
                "select * from `Cabinet`
                where `to` >= :to
                order by `from`"
            );
            $statement->execute(['to' => $to->format('Y-m-d')]);
        } else {
            $statement = $this->getDriver()->prepare(
                "select * from `Cabinet` order by `from`"
            );
            $statement->execute();
        }

        return array_map(function ($object) {
            return (new Hydrator\Cabinet())->hydrate($object, new Model\Cabinet());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchAllGenerator(): Generator
    {
        $statement = $this->getDriver()
            ->prepare('select * from `Cabinet` order by `cabinet_id`');
        $statement->execute();


        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Cabinet())->hydrate($object, new Model\Cabinet());
        }
        $statement->closeCursor();
        return null;
    }

    public function get(int $id): ?Model\Cabinet
    {
        $statement = $this->getDriver()->prepare("select * from `Cabinet` where cabinet_id = :id");
        $statement->execute(['id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\Cabinet())->hydrate($object, new Model\Cabinet())
            : null;
    }

    public function save(Model\Cabinet $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Cabinet', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableCabinetPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    public function update(Model\Cabinet $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Cabinet', $data, "cabinet_id={$data->getCabinetId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableCabinetPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    /**
     * @return \Althingi\Model\Cabinet[]
     */
    public function fetchByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare("
            select * from (
                select
                    A.`assembly_id`,
                    C.`cabinet_id`,
                    C.`title`,
                    C.`description`,
                    C.`from`,
                    C.`to`,
                    A.`from` as `assembly_from`,
                    A.`to` as `assembly_to`
                from Assembly A
                join Cabinet C on (
                    (A.`to` between C.`from` and C.`to`) or
                    (A.`to` > C.`from` and C.`to` is null) or
                    (A.`to` is null and C.`to` is null)
                  )
            ) as AssembliesAndCabinets where assembly_id = :assembly_id;
        ");

        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($object) {
            return (new Hydrator\Cabinet())->hydrate($object, new Model\Cabinet());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }
}
