<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 10/06/15
 * Time: 8:53 PM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;

/**
 * Class Party
 * @package Althingi\Service
 */
class Category implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one party.
     *
     * @param int $id
     * @return \stdClass
     */
    public function get($id)
    {
        $statement = $this->getDriver()->prepare('
            select * from `Category` where category_id = :category_id
        ');
        $statement->execute(['category_id' => $id]);
        return $this->decorate($statement->fetchObject());
    }

    public function fetchByAssembly($assemblyId)
    {
        $statement = $this->getDriver()->prepare('
            select count(*) as `count` , C.* from `Issue` I
            join `Category_has_Issue` CI on (CI.`issue_id` = I.`issue_id`)
            join `Category` C on (C.`category_id` = CI.`category_id`)
            where I.`assembly_id` = :assembly_id
            group by CI.`category_id`
            order by `count` desc;
        ');
        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    public function fetchByAssemblyAndIssue($assemblyId, $issueId)
    {
        $statement = $this->getDriver()->prepare('
            select C.* from `Category_has_Issue` CI
            join `Category` C on (C.`category_id` = CI.`category_id`)
            where CI.`assembly_id` = :assembly_id and CI.`issue_id` = :issue_id;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
        ]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }
    public function fetchByAssemblyIssueAndCategory($assemblyId, $issueId, $categoryId)
    {
        $statement = $this->getDriver()->prepare('
            select C.* from `Category_has_Issue` CI
            join `Category` C on (C.`category_id` = CI.`category_id`)
            where CI.`assembly_id` = :assembly_id and CI.`issue_id` = :issue_id and CI.`category_id` = :category_id;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'category_id' => $categoryId,
        ]);
        return $this->decorate($statement->fetchObject());
    }

    public function create($data)
    {
        $statement = $this->getDriver()->prepare($this->insertString('Category', $data));
        $statement->execute($this->convert($data));
        return $this->getDriver()->lastInsertId();
    }

    public function update($data)
    {
        $statement = $this->getDriver()->prepare(
            $this->updateString('Category', $data, "category_id = {$data->category_id}")
        );
        $statement->execute($this->convert($data));
        return $statement->rowCount();
    }

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->category_id = (int) $object->category_id;
        $object->super_category_id = (int) $object->super_category_id;

        if (property_exists($object, 'count')) {
            $object->count = (int) $object->count;
        }

        if (property_exists($object, 'party_id')) {
            $object->party_id = (int) $object->party_id;
        }

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
