<?php

namespace Althingi\Service;

use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Presenters\IndexableIssueLinkPresenter;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use PDO;

/**
 * Class Assembly
 * @package Althingi\Service
 */
class IssueLink implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /** @var \Zend\EventManager\EventManagerInterface */
    protected $events;


    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param string $category
     * @return array
     */
    public function fetchAll(int $assemblyId, int $issueId, string $category = 'A'): array
    {
        $statement = $this->getDriver()
            ->prepare("
                select I.* from IssueLink IL
                    join Issue I on (
                          IL.to_assembly_id = I.assembly_id 
                          and IL.to_issue_id = I.issue_id 
                          and IL.to_category = I.category
                      )
                where IL.from_assembly_id = :assembly_id 
                  and IL.from_issue_id = :issue_id 
                  and IL.from_category = :category;
            ");
        $statement->execute(['assembly_id' => $assemblyId, 'issue_id' => $issueId, 'category' => $category]);

        return array_map(function ($issue) {
            return (new Hydrator\Issue())->hydrate($issue, new Model\Issue());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Create one entry.
     *
     * @param \Althingi\Model\IssueLink $data
     * @return int affected rows
     */
    public function create(Model\IssueLink $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('IssueLink', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                AddEvent::class,
                new AddEvent(new IndexableIssueLinkPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

        return $this->getDriver()->lastInsertId();
    }

    /**
     * Save one entry.
     *
     * @param \Althingi\Model\IssueLink $data
     * @return int affected rows
     */
    public function save(Model\IssueLink $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('IssueLink', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventManager()
                    ->trigger(
                        AddEvent::class,
                        new AddEvent(new IndexableIssueLinkPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
            case 0:
            case 2:
                $this->getEventManager()
                    ->trigger(
                        UpdateEvent::class,
                        new UpdateEvent(new IndexableIssueLinkPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
        }
        return $statement->rowCount();
    }

    /**
     * Update one entry.
     *
     * @param \Althingi\Model\IssueLink|object $data
     * @return int affected rows
     */
    public function update(Model\IssueLink $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'IssueLink',
                $data,
                "to_assembly_id={$data->getAssemblyId()} and" .
                "to_issue_id={$data->getIssueId()} and" .
                "to_category={$data->getCategory()} and" .
                "from_assembly_id={$data->getFromAssemblyId()} and" .
                "from_issue_id={$data->getFromIssueId()} and" .
                "from_category={$data->getFromCategory()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                UpdateEvent::class,
                new UpdateEvent(new IndexableIssueLinkPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

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
