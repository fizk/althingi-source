<?php

namespace Althingi\Service;

use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Presenters\IndexableMinisterSittingPresenter;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use PDO;
use DateTime;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class MinisterSitting implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /** @var \Zend\EventManager\EventManagerInterface */
    protected $events;

    /**
     * Get one MinisterSitting.
     *
     * @param $id
     * @return null|\Althingi\Model\MinisterSitting
     */
    public function get(int $id): ? Model\MinisterSitting
    {
        $statement = $this->getDriver()->prepare("select * from `MinisterSitting` where minister_sitting_id = :id");
        $statement->execute(['id' => $id]);
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new Hydrator\MinisterSitting)->hydrate($assembly, new Model\MinisterSitting())
            : null;
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
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


    /**
     * Create one entry.
     *
     * @param \Althingi\Model\MinisterSitting $data
     * @return int affected rows
     */
    public function create(Model\MinisterSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('MinisterSitting', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $id = $this->getDriver()->lastInsertId();
        $data->setMinisterSittingId($id);
        $this->getEventManager()
            ->trigger(
                AddEvent::class,
                new AddEvent(new IndexableMinisterSittingPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

        return $id;
    }

    /**
     * Save one entry.
     *
     * @param \Althingi\Model\MinisterSitting $data
     * @return int affected rows
     */
    public function save(Model\MinisterSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('MinisterSitting', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventManager()
                    ->trigger(
                        AddEvent::class,
                        new AddEvent(new IndexableMinisterSittingPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
            case 0:
            case 2:
                $this->getEventManager()
                    ->trigger(
                        UpdateEvent::class,
                        new UpdateEvent(new IndexableMinisterSittingPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
        }
        return $statement->rowCount();
    }

    /**
     * Update one entry.
     *
     * @param \Althingi\Model\MinisterSitting|object $data
     * @return int affected rows
     */
    public function update(Model\MinisterSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('MinisterSitting', $data, "minister_sitting_id={$data->getMinisterSittingId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                UpdateEvent::class,
                new UpdateEvent(new IndexableMinisterSittingPresenter($data)),
                ['rows' => $statement->rowCount()]
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
        $statement = $this->getDriver()->prepare(
            "delete from `MinisterSitting` where minister_sitting_id = :minister_sitting_id"
        );
        $statement->execute(['minister_sitting_id' => $id]);

        return $statement->rowCount();
    }

    /**
     * @param int $assemblyId
     * @param int $ministryId
     * @param $congressmanId
     * @param DateTime $from
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
