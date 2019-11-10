<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Presenters\IndexableSessionPresenter;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use PDO;
use DateTime;

/**
 * Class CommitteeSitting
 * @package Althingi\Service
 */
class CommitteeSitting implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /** @var \Zend\EventManager\EventManagerInterface */
    protected $events;

    /**
     * Get one Congressman's Session.
     *
     * @param int $id
     * @return null|\Althingi\Model\Session
     */
    public function get(int $id): ? Model\CommitteeSitting
    {
        $statement = $this->getDriver()->prepare(
            "select * from `CommitteeSitting` where committee_sitting_id = :committee_sitting_id"
        );
        $statement->execute(['committee_sitting_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\CommitteeSitting())->hydrate($object, new Model\CommitteeSitting())
            : null;
    }

    /**
     * Create one entry. Accepts object from
     * corresponding Form.
     *
     * @param \Althingi\Model\CommitteeSitting $data
     * @return int affected rows
     */
    public function create(Model\CommitteeSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('CommitteeSitting', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $id = $this->getDriver()->lastInsertId();
        $data->setCommitteeSittingId($id);

//        $this->getEventManager()
//            ->trigger(
//                AddEvent::class,
//                new AddEvent(new IndexableSessionPresenter($data)),
//                ['rows' => $statement->rowCount()]
//            );

        return $id;
    }

    /**
     * Update one Congressman's Session. Accepts object from
     * corresponding Form.
     *
     * @param \Althingi\Model\CommitteeSitting | object $data
     * @return int
     */
    public function update(Model\CommitteeSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('CommitteeSitting', $data, "committee_sitting_id={$data->getCommitteeSittingId()}")
        );
        $statement->execute($this->toSqlValues($data));

//        $this->getEventManager()
//            ->trigger(
//                UpdateEvent::class,
//                new UpdateEvent(new IndexableSessionPresenter($data)),
//                ['rows' => $statement->rowCount()]
//            );

        return $statement->rowCount();
    }

    public function fetchByCongressman(int $congressmanId)
    {
        $statement = $this->getDriver()->prepare(
            "select * from `CommitteeSitting` where congressman_id = :congressman_id order by `from`"
        );
        $statement->execute(['congressman_id' => $congressmanId]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($object) {
            return (new Hydrator\CommitteeSitting())->hydrate($object, new Model\CommitteeSitting());
        }, $result);
    }

    /**
     * @param int $congressmanId
     * @param int $committeeId
     * @param int $assemblyId
     * @param DateTime $from
     * @return int
     */
    public function getIdentifier(int $congressmanId, int $committeeId, int $assemblyId, DateTime $from): int
    {
        $statement = $this->getDriver()->prepare('
            select `committee_sitting_id` from `CommitteeSitting`
            where `congressman_id` = :congressman_id and 
                `committee_id` = :committee_id and 
                `assembly_id` = :assembly_id and 
                `from` = :from;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'committee_id' => $committeeId,
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
