<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableMinistryPresenter;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use Generator;
use PDO;

class Ministry implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $id): ?Model\Ministry
    {
        $statement = $this->getDriver()->prepare("select * from `Ministry` where ministry_id = :id");
        $statement->execute(['id' => $id]);
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new Hydrator\Ministry())->hydrate($assembly, new Model\Ministry())
            : null;
    }

    /**
     * @return \Althingi\Model\Ministry[]
     */
    public function fetchAll(): array
    {
        $statement = $this->getDriver()->prepare("select * from `Ministry`");
        $statement->execute();

        return array_map(function ($assembly) {
            return (new Hydrator\Ministry())->hydrate($assembly, new Model\Ministry());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchAllGenerator(): Generator
    {
        $statement = $this->getDriver()
            ->prepare('select * from `Ministry` order by `ministry_id`');
        $statement->execute();


        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Ministry())->hydrate($object, new Model\Ministry());
        }
        $statement->closeCursor();
        return null;
    }

    /**
     * @return \Althingi\Model\Ministry[]
     */
    public function fetchByCongressmanAssembly(int $assemblyId, int $congressmanId)
    {
        $statement = $this->getDriver()->prepare(
            "select DISTINCT M.* from MinisterSession MS
                join Ministry M on MS.ministry_id = M.ministry_id
            where assembly_id = :assembly_id and congressman_id = :congressman_id"
        );
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);

        return array_map(function ($assembly) {
            return (new Hydrator\Ministry())->hydrate($assembly, new Model\Ministry());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getByCongressmanAssembly(int $assemblyId, int $congressmanId, int $ministryId): ?Model\Ministry
    {
        $statement = $this->getDriver()->prepare(
            "select DISTINCT M.* from MinisterSession MS
                join Ministry M on MS.ministry_id = M.ministry_id
                where MS.assembly_id = :assembly_id
                    and MS.congressman_id = :congressman_id
                    and MS.ministry_id = :ministry_id"
        );
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
            'ministry_id' => $ministryId,
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\Ministry())->hydrate($object, new Model\Ministry())
            : null;
    }

    public function count(): int
    {
        $statement = $this->getDriver()->prepare("select count(*) from `Ministry`");
        $statement->execute();

        return (int) $statement->fetchColumn(0);
    }

    public function create(Model\Ministry $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Ministry', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableMinistryPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\Ministry $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Ministry', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableMinistryPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableMinistryPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\Ministry $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Ministry', $data, "ministry_id={$data->getMinistryId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableMinistryPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    public function delete(int $id): int
    {
        $statement = $this->getDriver()->prepare("delete from `Ministry` where ministry_id = :ministry_id");
        $statement->execute(['ministry_id' => $id]);

        return $statement->rowCount();
    }
}
