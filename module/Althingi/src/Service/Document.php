<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Presenters\IndexableDocumentPresenter;
use PDO;

class Document implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param int $documentId
     * @return \Althingi\Model\Document|null
     */
    public function get(int $assemblyId, int $issueId, int $documentId): ? Model\Document
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
            ? (new Hydrator\Document())->hydrate($object, new Model\Document())
            : null ;
    }

    /**
     * @param $assemblyId
     * @param $issueId
     * @return \Althingi\Model\ValueAndCount[] |null
     */
    public function countTypeByIssue($assemblyId, $issueId): array
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
            return (new Hydrator\ValueAndCount())->hydrate($object, new Model\ValueAndCount());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param \Althingi\Model\Document $data
     * @return int
     */
    public function create(Model\Document $data): int
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
    public function save(Model\Document $data): int
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
     * @param \Althingi\Model\Document | object $data
     * @return int
     */
    public function update(Model\Document $data): int
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
            return (new Hydrator\Document())->hydrate($object, new Model\Document());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }
}
