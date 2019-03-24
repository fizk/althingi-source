<?php

namespace Althingi\Service;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Presenters\IndexableCongressmanDocumentPresenter;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use PDO;

class CongressmanDocument implements DatabaseAwareInterface, EventsAwareInterface
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
     * @param int $congressmanId
     * @return \Althingi\Model\CongressmanDocument|null
     */
    public function get(int $assemblyId, int $issueId, int $documentId, int $congressmanId): ? Model\CongressmanDocument
    {
        $statement = $this->getDriver()->prepare("
            select * from `Document_has_Congressman` D 
            where D.`assembly_id` = :assembly_id 
              and D.`issue_id` = :issue_id 
              and D.`document_id` = :document_id 
              and D.`congressman_id` = :congressman_id
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'document_id' => $documentId,
            'congressman_id' => $congressmanId,
        ]);

        $congressmanDocument = $statement->fetch(PDO::FETCH_ASSOC);
        return $congressmanDocument
            ? (new Hydrator\CongressmanDocument())->hydrate($congressmanDocument, new Model\CongressmanDocument())
            : null ;
    }

    public function fetchByDocument(int $assemblyId, int $issueId, int $documentId): array
    {
        $statement = $this->getDriver()->prepare("
            select DC.* from Document_has_Congressman DC
              where DC.assembly_id = :assembly_id and DC.issue_id = :issue_id and DC.document_id = :document_id
            order by DC.`order`;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'document_id' => $documentId,
        ]);

        return array_map(function ($congressmanDocument) {
            return (new Hydrator\CongressmanDocument())->hydrate($congressmanDocument, new Model\CongressmanDocument());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param int $documentId
     * @return \Althingi\Model\CongressmanDocument|null
     */
    public function countProponents(int $assemblyId, int $issueId, int $documentId): ? int
    {
        $statement = $this->getDriver()->prepare("
            select count(*) 
            from Document_has_Congressman 
            where assembly_id = :assembly_id and issue_id = :issue_id and document_id = :document_id
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'document_id' => $documentId,
        ]);

        return $statement->fetchColumn(0);
    }

    public function fetchProponents($assemblyId, $issueId, $documentId): ? Model\CongressmanValue
    {
        $statement = $this->getDriver()->prepare("
            select C.*, DC.`order` as `value` from Document_has_Congressman DC
              join Congressman C on (DC.congressman_id = C.congressman_id)
              where DC.assembly_id = :assembly_id and DC.issue_id = :issue_id and DC.document_id = :document_id
            order by DC.`order`;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'document_id' => $documentId,
        ]);

        $congressmanDocument = $statement->fetch(PDO::FETCH_ASSOC);
        return $congressmanDocument
            ? (new Hydrator\CongressmanValue())->hydrate($congressmanDocument, new Model\CongressmanValue())
            : null ;
    }

    /**
     * @param \Althingi\Model\CongressmanDocument $data
     * @return int
     */
    public function create(Model\CongressmanDocument $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Document_has_Congressman', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                AddEvent::class,
                new AddEvent(new IndexableCongressmanDocumentPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\CongressmanDocument $data
     * @return int
     */
    public function save(Model\CongressmanDocument $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Document_has_Congressman', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventManager()
                    ->trigger(
                        AddEvent::class,
                        new AddEvent(new IndexableCongressmanDocumentPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
            case 0:
            case 2:
                $this->getEventManager()
                    ->trigger(
                        UpdateEvent::class,
                        new UpdateEvent(new IndexableCongressmanDocumentPresenter($data)),
                        ['rows' => $statement->rowCount()]
                    );
                break;
        }
        return $statement->rowCount();
    }

    /**
     * @param \Althingi\Model\CongressmanDocument | object $data
     * @return int
     */
    public function update(Model\CongressmanDocument $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'Document_has_Congressman',
                $data,
                "assembly_id={$data->getAssemblyId()} " .
                "and issue_id={$data->getIssueId()} " .
                "and document_id={$data->getDocumentId()} " .
                "and congressman_id={$data->getCongressmanId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                UpdateEvent::class,
                new UpdateEvent(new IndexableCongressmanDocumentPresenter($data)),
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
