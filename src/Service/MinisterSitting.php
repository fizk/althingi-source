<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableMinisterSittingPresenter;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use PDO;
use DateTime;
use Generator;

class MinisterSitting implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $id): ?Model\MinisterSitting
    {
        $statement = $this->getDriver()->prepare("select * from `MinisterSitting` where minister_sitting_id = :id");
        $statement->execute(['id' => $id]);
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new Hydrator\MinisterSitting())->hydrate($assembly, new Model\MinisterSitting())
            : null;
    }

    /**
     * @return \Althingi\Model\MinisterSitting[]
     */
    public function fetchByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare("
            select * from MinisterSitting where assembly_id = :assembly_id;
        ");
        $statement->execute(['assembly_id' => $assemblyId,]);
        $sittings = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($object) {
            return (new Hydrator\MinisterSitting())->hydrate($object, new Model\MinisterSitting());
        }, $sittings);
    }

    /**
     * @return \Althingi\Model\MinisterSitting[]
     */
    public function fetchByCongressmanAssembly(int $assemblyId, int $congressmanId)
    {
        $statement = $this->getDriver()->prepare("
            select * from `MinisterSitting`
                where assembly_id = :assembly_id and congressman_id = :congressman_id
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);
        $sittings = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($object) {
            return (new Hydrator\MinisterSitting())->hydrate($object, new Model\MinisterSitting());
        }, $sittings);
    }

    public function fetchAllGenerator(?int $assemblyId = null): Generator
    {
        if ($assemblyId) {
            $statement = $this->getDriver()
                ->prepare(
                    'select * from MinisterSitting where assembly_id = :assembly_id order by `minister_sitting_id`'
                );
            $statement->execute(['assembly_id' => $assemblyId]);
        } else {
            $statement = $this->getDriver()
                ->prepare('select * from MinisterSitting order by `minister_sitting_id`');
            $statement->execute();
        }

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\MinisterSitting())->hydrate($object, new Model\MinisterSitting());
        }
        $statement->closeCursor();
        return null;
    }

    public function create(Model\MinisterSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('MinisterSitting', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $id = $this->getDriver()->lastInsertId();
        $data->setMinisterSittingId($id);

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableMinisterSittingPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $id;
    }

    public function save(Model\MinisterSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('MinisterSitting', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableMinisterSittingPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableMinisterSittingPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\MinisterSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('MinisterSitting', $data, "minister_sitting_id={$data->getMinisterSittingId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableMinisterSittingPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    public function delete(int $id): int
    {
        $statement = $this->getDriver()->prepare(
            "delete from `MinisterSitting` where minister_sitting_id = :minister_sitting_id"
        );
        $statement->execute(['minister_sitting_id' => $id]);

        return $statement->rowCount();
    }

    /**
     * @return mixed
     */
    public function getIdentifier(int $assemblyId, int $ministryId, $congressmanId, DateTime $from)
    {
        $statement = $this->getDriver()->prepare('
            select `minister_sitting_id` from `MinisterSitting`
            where `congressman_id` = :congressman_id and
                `ministry_id` = :ministry_id and
                `assembly_id` = :assembly_id and
                `from` = :from;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'ministry_id' => $ministryId,
            'assembly_id' => $assemblyId,
            'from' => $from->format('Y-m-d'),
        ]);
        return $statement->fetchColumn(0);
    }
}
