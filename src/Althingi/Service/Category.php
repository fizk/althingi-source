<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 10/06/15
 * Time: 8:53 PM
 */

namespace Althingi\Service;

use PDO;
use Althingi\Lib\DatabaseAwareInterface;
use Althingi\Model\Category as CategoryModel;
use Althingi\Hydrator\Category as CategoryHydrator;
use Althingi\Hydrator\CategoryAndCount as CategoryAndCountHydrator;
use Althingi\Model\CategoryAndCount as CategoryAndCountModel;

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
     * @return \Althingi\Model\Category
     */
    public function get(int $id): ?CategoryModel
    {
        $statement = $this->getDriver()->prepare('
            select * from `Category` where category_id = :category_id
        ');
        $statement->execute(['category_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new CategoryHydrator())->hydrate($object, new CategoryModel())
            : null;
    }

    /**
     * @param $assemblyId
     * @return \Althingi\Model\CategoryAndCount[]
     */
    public function fetchByAssembly(int $assemblyId): array
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

        return array_map(function ($object) {
            return (new CategoryAndCountHydrator())->hydrate($object, new CategoryAndCountModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @return \Althingi\Model\Category[]
     */
    public function fetchByAssemblyAndIssue(int $assemblyId, int $issueId): array
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
        return array_map(function ($object) {
            return (new CategoryHydrator())->hydrate($object, new CategoryModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param int $categoryId
     * @return \Althingi\Model\Category|null
     */
    public function fetchByAssemblyIssueAndCategory(int $assemblyId, int $issueId, int $categoryId): ?CategoryModel
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
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new CategoryHydrator())->hydrate($object, new CategoryModel())
            : null;
    }

    /**
     * @param \Althingi\Model\Category $data
     * @return int
     */
    public function create(CategoryModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Category', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\Category $data
     * @return int
     */
    public function update(CategoryModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Category', $data, "category_id={$data->getCategoryId()}")
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
