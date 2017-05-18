<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Hydrator\CongressmanDocument as CongressmanDocumentHydrator;
use Althingi\Model\CongressmanDocument as CongressmanDocumentModel;
use PDO;

class CongressmanDocument implements DatabaseAwareInterface
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
     * @param int $congressmanId
     * @return \Althingi\Model\CongressmanDocument|null
     */
    public function get(int $assemblyId, int $issueId, int $documentId, int $congressmanId): ?CongressmanDocumentModel
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
            ? (new CongressmanDocumentHydrator())->hydrate($congressmanDocument, new CongressmanDocumentModel())
            : null ;
    }

    /**
     * @param \Althingi\Model\CongressmanDocument $data
     * @return int
     */
    public function create(CongressmanDocumentModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Document_has_Congressman', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\CongressmanDocument $data
     * @return int
     */
    public function update(CongressmanDocumentModel $data): int
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

        return $statement->rowCount();
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
