<?php

namespace Althingi\Service;

use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\{DatabaseAwareInterface, EventsAwareInterface};
use Althingi\Model\KindEnum;
use Althingi\Presenters\IndexableCommitteeDocumentPresenter;
use Exception;
use Generator;
use PDO;

class CommitteeDocument implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $id): ?Model\CommitteeDocument
    {
        $statement = $this->getDriver()->prepare("
            select * from `Document_has_Committee`
            where document_committee_id = :document_committee_id
        ");
        $statement->execute(['document_committee_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\CommitteeDocument())->hydrate($object, new Model\CommitteeDocument())
            : null;
    }

    public function fetchAllGenerator(?int $assemblyId = null, ?int $issueId = null, ?int $documentId = null): Generator
    {
        if ($assemblyId === null) {
            $statement = $this->getDriver()
                ->prepare('select * from `Document_has_Committee`');
            $statement->execute();
        } elseif ($assemblyId !== null &&  $issueId === null) {
            $statement = $this->getDriver()
                ->prepare('
                    select * from `Document_has_Committee` where
                    `assembly_id` = :assembly_id
                ');
            $statement->execute([
                'assembly_id' => $assemblyId
            ]);
        } elseif ($assemblyId !== null &&  $issueId !== null && $documentId === null) {
            $statement = $this->getDriver()
                ->prepare('
                    select * from `Document_has_Committee` where
                    `assembly_id` = :assembly_id and
                    `issue_id` = :issue_id
                ');
            $statement->execute([
                'assembly_id' => $assemblyId,
                'issue_id' => $issueId
            ]);
        } elseif ($assemblyId !== null &&  $issueId !== null && $documentId !== null) {
            $statement = $this->getDriver()
                ->prepare('
                    select * from `Document_has_Committee` where
                    `assembly_id` = :assembly_id and
                    `issue_id` = :issue_id and
                    `document_id` = :document_id
                ');
            $statement->execute([
                'assembly_id' => $assemblyId,
                'issue_id' => $issueId,
                'document_id' => $documentId,
            ]);
        } else {
            throw new Exception('Parameter missing');
        }

        $statement = $this->getDriver()
            ->prepare('select * from `Document_has_Committee`');
        $statement->execute();


        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\CommitteeDocument())->hydrate($object, new Model\CommitteeDocument());
        }
        $statement->closeCursor();
        return null;
    }

    /**
     * @return Model\CommitteeDocument[]
     */
    public function fetchByDocument(int $assemblyId, $issueId, $documentId): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Document_has_Committee`
            where assembly_id = :assembly_id and
                issue_id = :issue_id and
                kind = :kind and
                document_id = :document_id
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'kind' => KindEnum::A->value,
            'document_id' => $documentId,
        ]);

        return array_map(function ($object) {
            return (new Hydrator\CommitteeDocument())->hydrate($object, new Model\CommitteeDocument());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(Model\CommitteeDocument $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Document_has_Committee', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $id = $this->getDriver()->lastInsertId();
        $data->setDocumentCommitteeId($id);

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableCommitteeDocumentPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $id;
    }

    public function update(Model\CommitteeDocument $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'Document_has_Committee',
                $data,
                "document_committee_id={$data->getDocumentCommitteeId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableCommitteeDocumentPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $statement->rowCount();
    }

    public function getIdentifier(
        int $documentId,
        int $assemblyId,
        int $issueId,
        KindEnum $kind,
        int $committeeId,
        string $part
    ): int {
        $statement = $this->getDriver()->prepare('
            select `committee_sitting_id` from `CommitteeSitting`
            where `assembly_id` = :assembly_id and,
                `document_id` = :document_id and,
                `issue_id` = :issue_id and,
                `kind` = :kind and,
                `committee_id` = :committee_id and,
                `part` = :part
            ;
        ');
        $statement->execute([
            'document_id' => $documentId,
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'kind' => $kind->value,
            'committee_id' => $committeeId,
            'part' => $part,
        ]);
        return $statement->fetchColumn(0);
    }
}
