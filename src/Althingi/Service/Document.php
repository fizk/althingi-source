<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 22/03/2016
 * Time: 11:03 AM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;

class Document implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function get($assemblyId, $issueId, $documentId)
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

        $assembly = $statement->fetchObject();
        return $this->decorate($assembly);
    }

    public function create($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->insertString('Document', $data));
        $statement->execute($this->convert($data));
    }

    public function update($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->updateString(
                'Document',
                $data,
                "assembly_id={$data->assembly_id} and issue_id={$data->issue_id} and document_id={$data->document_id}"
            ));
        $statement->execute($this->convert($data));
    }

    public function fetchByIssue($assemblyId, $issueId)
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

        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->document_id = (int) $object->document_id;
        $object->issue_id = (int) $object->issue_id;
        $object->assembly_id = (int) $object->assembly_id;

        return $object;
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
