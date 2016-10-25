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

class Proponent implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function get($assemblyId, $issueId, $documentId, $congressmanId)
    {
        $statement = $this->getDriver()->prepare("
            select * from `Document_has_Congressman` D 
            where D.`assembly_id` = :assembly_id and D.`issue_id` = :issue_id and D.`document_id` = :document_id and D.`congressman_id` = :congressman_id
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'document_id' => $documentId,
            'congressman_id' => $congressmanId,
        ]);

        $assembly = $statement->fetchObject();
        return $this->decorate($assembly);
    }

    public function create($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->insertString('Document_has_Congressman', $data));
        $statement->execute($this->convert($data));
    }

    public function update($data)
    {
        $statement = $this
            ->getDriver()
            ->prepare($this->updateString(
                'Document_has_Congressman',
                $data,
                "assembly_id={$data->assembly_id} and issue_id={$data->issue_id} and document_id={$data->document_id} and congressman_id={$data->congressman_id}"
            ));
        $statement->execute($this->convert($data));
    }

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->assembly_id = (int) $object->assembly_id;
        $object->issue_id = (int) $object->issue_id;
        $object->document_id = (int) $object->document_id;
        $object->congressman_id = (int) $object->congressman_id;

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
