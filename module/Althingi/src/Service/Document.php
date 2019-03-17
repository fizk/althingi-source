<?php

namespace Althingi\Service;

use Althingi\Lib\EventsAwareInterface;
use Althingi\Lib\DatabaseAwareInterface;

use Althingi\Model\Document as DocumentModel;
use Althingi\Hydrator\Document as DocumentHydrator;

use Althingi\Model\ValueAndCount as ValueAndCountModel;
use Althingi\Hydrator\ValueAndCount as ValueAndCountHydrator;

use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Presenters\IndexableDocumentPresenter;
use PDO;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

class Document implements DatabaseAwareInterface, EventsAwareInterface
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
     * @param int $documentId
     * @return \Althingi\Model\Document|null
     */
    public function get(int $assemblyId, int $issueId, int $documentId): ?DocumentModel
    {
        $statement = $this->getDriver()->prepare("
            select * from `Document` D 
            where D.`assembly_id` = :assembly_id and D.`issue_id` = :issue_id and D.`document_id` = :document_id
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'document_id' => $documentId
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new DocumentHydrator())->hydrate($object, new DocumentModel())
            : null ;
    }

    public function countTypeByIssue($assemblyId, $issueId)
    {
        $statement = $this->getDriver()->prepare("
            select count(*) as `count`, `type` as `value` from `Document`
             where assembly_id = :assembly_id and issue_id = :issue_id
             group by `type`
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
        ]);


        return array_map(function ($object) {
            return (new ValueAndCountHydrator())->hydrate($object, new ValueAndCountModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param \Althingi\Model\Document $data
     * @return int
     */
    public function create(DocumentModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Document', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                AddEvent::class,
                new AddEvent(new IndexableDocumentPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\Document $data
     * @return int
     */
    public function save(DocumentModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Document', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventManager()
                    ->trigger(
                        AddEvent::class,
                        new AddEvent(new IndexableDocumentPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
            case 0:
            case 2:
                $this->getEventManager()
                    ->trigger(
                        UpdateEvent::class,
                        new UpdateEvent(new IndexableDocumentPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
        }
        return $statement->rowCount();
    }

    /**
     * @param \Althingi\Model\Document $data
     * @return int
     */
    public function update(DocumentModel$data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'Document',
                $data,
                "assembly_id={$data->getAssemblyId()} " .
                "and issue_id={$data->getIssueId()} " .
                "and document_id={$data->getDocumentId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                UpdateEvent::class,
                new UpdateEvent(new IndexableDocumentPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

        return $statement->rowCount();
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @return \Althingi\Model\Document[]
     */
    public function fetchByIssue(int $assemblyId, int $issueId): array
    {
        $statement = $this->getDriver()->prepare('
            select * from `Document`
            where assembly_id = :assembly_id and issue_id = :issue_id
            order by `document_id` asc;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
        ]);

        return array_map(function ($object) {
            return (new DocumentHydrator())->hydrate($object, new DocumentModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
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
