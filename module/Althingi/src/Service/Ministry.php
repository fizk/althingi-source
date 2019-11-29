<?php

namespace Althingi\Service;

use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Presenters\IndexableMinistryPresenter;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use PDO;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class Ministry implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /** @var \Zend\EventManager\EventManagerInterface */
    protected $events;

    /**
     * Get one Assembly.
     *
     * @param $id
     * @return null|\Althingi\Model\Ministry
     */
    public function get(int $id): ? Model\Ministry
    {
        $statement = $this->getDriver()->prepare("select * from `Ministry` where ministry_id = :id");
        $statement->execute(['id' => $id]);
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new Hydrator\Ministry)->hydrate($assembly, new Model\Ministry())
            : null;
    }

    /**
     * Get all Assemblies.
     *
     * @return \Althingi\Model\Ministry[]
     */
    public function fetchAll(): array
    {
        $statement = $this->getDriver()->prepare("select * from `Ministry`");
        $statement->execute();

        return array_map(function ($assembly) {
            return (new Hydrator\Ministry)->hydrate($assembly, new Model\Ministry());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
     * @return \Althingi\Model\Ministry[]
     */
    public function fetchByCongressmanAssembly(int $assemblyId, int $congressmanId)
    {
        $statement = $this->getDriver()->prepare(
            "select DISTINCT M.* from MinisterSitting MS
                join Ministry M on MS.ministry_id = M.ministry_id
            where assembly_id = :assembly_id and congressman_id = :congressman_id"
        );
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);

        return array_map(function ($assembly) {
            return (new Hydrator\Ministry)->hydrate($assembly, new Model\Ministry());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
     * @param int $ministryId
     * @return \Althingi\Model\Ministry
     */
    public function getByCongressmanAssembly(int $assemblyId, int $congressmanId, int $ministryId)
    {
        $statement = $this->getDriver()->prepare(
            "select DISTINCT M.* from MinisterSitting MS
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
            ? (new Hydrator\Ministry)->hydrate($object, new Model\Ministry())
            : null;
    }

    /**
     * Count all assemblies.
     *
     * @return int
     */
    public function count(): int
    {
        $statement = $this->getDriver()->prepare("select count(*) from `Ministry`");
        $statement->execute();

        return (int) $statement->fetchColumn(0);
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\Ministry $data
     * @return int affected rows
     */
    public function create(Model\Ministry $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Ministry', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                AddEvent::class,
                new AddEvent(new IndexableMinistryPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

        return $this->getDriver()->lastInsertId();
    }

    /**
     * Save one entry.
     *
     * @param \Althingi\Model\Ministry $data
     * @return int affected rows
     */
    public function save(Model\Ministry $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Ministry', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventManager()
                    ->trigger(
                        AddEvent::class,
                        new AddEvent(new IndexableMinistryPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
            case 0:
            case 2:
                $this->getEventManager()
                    ->trigger(
                        UpdateEvent::class,
                        new UpdateEvent(new IndexableMinistryPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
        }
        return $statement->rowCount();
    }

    /**
     * Update one entry.
     *
     * @param \Althingi\Model\Ministry|object $data
     * @return int affected rows
     */
    public function update(Model\Ministry $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Ministry', $data, "ministry_id={$data->getMinistryId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                UpdateEvent::class,
                new UpdateEvent(new IndexableMinistryPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

        return $statement->rowCount();
    }

    /**
     * Delete one ministry_id.
     * Should return 1, for one assembly deleted.
     *
     * @param int $id
     * @return int
     */
    public function delete(int $id): int
    {
        $statement = $this->getDriver()->prepare("delete from `Ministry` where ministry_id = :ministry_id");
        $statement->execute(['ministry_id' => $id]);

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

    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;
        return $this;
    }

    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
