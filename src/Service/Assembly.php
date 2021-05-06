<?php

namespace Althingi\Service;

use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Presenters\IndexableAssemblyPresenter;
use PDO;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class Assembly implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    const ALLOWED_TYPES = ['a', 'b', 'l', 'm', 'q', 's'];
    const MAX_ROW_COUNT = '18446744073709551615';

    /**
     * Get one Assembly.
     *
     * @param $id
     * @return null|\Althingi\Model\Assembly
     */
    public function get(int $id): ? Model\Assembly
    {
        $statement = $this->getDriver()->prepare("select * from `Assembly` where assembly_id = :id");
        $statement->execute(['id' => $id]);
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new Hydrator\Assembly)->hydrate($assembly, new Model\Assembly())
            : null;
    }

    /**
     * Get current assembly.
     *
     * @return null|\Althingi\Model\Assembly
     */
    public function getCurrent(): ? Model\Assembly
    {
        $statement = $this->getDriver()->prepare("select * from `Assembly` order by `assembly_id` desc limit 0, 1");
        $statement->execute();
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new Hydrator\Assembly)->hydrate($assembly, new Model\Assembly())
            : null;
    }

    /**
     * Get all Assemblies.
     *
     * @param int $offset
     * @param int $size
     * @param string $order
     * @return \Althingi\Model\Assembly[]
     */
    public function fetchAll(int $offset = 0, int $size = null, string $order = 'asc'): array
    {
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';
        $size = $size ? : self::MAX_ROW_COUNT;

        $statement = $this->getDriver()
            ->prepare("select * from `Assembly` A order by A.`from` {$order} limit {$offset}, {$size}");
        $statement->execute();

        return array_map(function ($assembly) {
            return (new Hydrator\Assembly)->hydrate($assembly, new Model\Assembly());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchByCabinet(int $id)
    {
        $statement = $this->getDriver()->prepare("
            select * from (
                select
                    A.*,
                    C.cabinet_id
                from Assembly A
                join Cabinet C on (
                    (A.`to` between C.`from` and C.`to`) or
                    (A.`to` > C.`from` and C.`to` is null) or
                    (A.`to` is null and C.`to` is null)
                )
            ) as AssemblyAndCabinet where cabinet_id = :id;
        ");
        $statement->execute(['id' => $id]);

        return array_map(function ($assembly) {
            return (new Hydrator\Assembly)->hydrate($assembly, new Model\Assembly());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return string[]
     */
    public function fetchTypes(): array
    {
        return self::ALLOWED_TYPES;
    }

    /**
     * Count all assemblies.
     *
     * @return int
     */
    public function count(): int
    {
        $statement = $this->getDriver()->prepare("select count(*) from `Assembly` A");
        $statement->execute();

        return (int) $statement->fetchColumn(0);
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\Assembly $data
     * @return int affected rows
     */
    public function create(Model\Assembly $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Assembly', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableAssemblyPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $this->getDriver()->lastInsertId();
    }

    /**
     * Save one entry.
     *
     * @param \Althingi\Model\Assembly $data
     * @return int affected rows
     */
    public function save(Model\Assembly $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Assembly', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableAssemblyPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableAssemblyPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
        }
        return $statement->rowCount();
    }

    /**
     * Update one entry.
     *
     * @param \Althingi\Model\Assembly|object $data
     * @return int affected rows
     */
    public function update(Model\Assembly $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Assembly', $data, "assembly_id={$data->getAssemblyId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableAssemblyPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $statement->rowCount();
    }

    /**
     * Delete one Assembly.
     * Should return 1, for one assembly deleted.
     *
     * @param int $id
     * @return int
     */
    public function delete(int $id): int
    {
        $statement = $this->getDriver()->prepare("delete from `Assembly` where assembly_id = :assembly_id");
        $statement->execute(['assembly_id' => $id]);

        return $statement->rowCount();
    }
}
