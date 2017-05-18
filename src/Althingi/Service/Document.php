<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\Document as DocumentModel;
use Althingi\Hydrator\Document as DocumentHydrator;
use PDO;

class Document implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

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

        return $this->getDriver()->lastInsertId();
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
                "assembly_id={$data->getAssemblyId()} and issue_id={$data->getIssueId()} and document_id={$data->getDocumentId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

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
     */
    public function setDriver(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getDriver()
    {
        return $this->pdo;
    }
}
