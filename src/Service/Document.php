<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableDocumentPresenter;
use Althingi\Injector\{DatabaseAwareInterface, EventsAwareInterface};
use Althingi\Model\KindEnum;
use Exception;
use Generator;
use PDO;
use PDOException;

class Document implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $assemblyId, int $issueId, int $documentId): ?Model\Document
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

    public function getPrimaryDocument(int $assemblyId, int $issueId): ?Model\Document
    {
        $statement = $this->getDriver()->prepare("
            select * from `Document` where
            `assembly_id` = :assembly_id  and
            `issue_id` = :issue_id and
            `kind` = :kind
            order by `date`
            limit 0, 1
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'kind' => KindEnum::A
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Document())->hydrate($object, new Model\Document())
            : null;
    }

    /**
     * @return \Althingi\Model\Document[]
     */
    public function fetchAllGenerator(?int $assemblyId = null, ?int $issueId = null): Generator
    {
        if ($assemblyId === null) {
            $statement = $this->getDriver()
                ->prepare('select * from `Document` order by `date`');
            $statement->execute();
        } elseif ($assemblyId !== null && $issueId === null) {
            $statement = $this->getDriver()
                ->prepare('
                    select * from `Document` where
                        `assembly_id` = :assembly_id
                    order by `date`
                ');
            $statement->execute([
                'assembly_id' => $assemblyId
            ]);
        } elseif ($assemblyId !== null && $issueId !== null) {
            $statement = $this->getDriver()
                ->prepare('
                    select * from `Document` where
                        `assembly_id` = :assembly_id and
                        `issue_id` = :issue_id
                    order by `date`
                ');
            $statement->execute([
                'assembly_id' => $assemblyId,
                'issue_id' => $issueId
            ]);
        } else {
            throw new Exception('Invalid parameters');
        }

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Document())->hydrate($object, new Model\Document());
        }
        $statement->closeCursor();
        return null;
    }

    /**
     * @return \Althingi\Model\ValueAndCount[]
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

    public function create(Model\Document $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Document', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableDocumentPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\Document $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Document', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableDocumentPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableDocumentPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
        }
        return $statement->rowCount();
    }

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

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableDocumentPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    /**
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
