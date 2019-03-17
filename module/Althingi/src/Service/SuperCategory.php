<?php

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;
use Althingi\Model\SuperCategory as SuperCategoryModel;
use Althingi\Hydrator\SuperCategory as SuperCategoryHydrator;

/**
 * Class Party
 * @package Althingi\Service
 */
class SuperCategory implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Get one super category.
     *
     * @param int $id
     * @return \Althingi\Model\SuperCategory
     */
    public function get(int $id): ?SuperCategoryModel
    {
        $statement = $this->getDriver()->prepare('
            select * from `SuperCategory` where super_category_id = :super_category_id
        ');
        $statement->execute(['super_category_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new SuperCategoryHydrator())->hydrate($object, new SuperCategoryModel())
            : null;
    }

    /**
     * Get all super categories on an issue.
     *
     * @param int $assemblyId
     * @param int $issueId
     * @param string $category
     * @return array
     */
    public function fetchByIssue(int $assemblyId, int $issueId, string$category = 'A')
    {
        $statement = $this->getDriver()->prepare('
            select SC.* from Category_has_Issue CI
              join Category C on (CI.category_id = C.category_id)
              join SuperCategory SC on (C.super_category_id = SC.super_category_id)
            where CI.assembly_id = :assembly_id and CI.issue_id = :issue_id and CI.category = :category
            group by C.super_category_id;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'category' => $category,
        ]);

        return array_map(function ($object) {
            return (new SuperCategoryHydrator())->hydrate($object, new SuperCategoryModel());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param SuperCategoryModel $data
     * @return int
     */
    public function create(SuperCategoryModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('SuperCategory', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param SuperCategoryModel $data
     * @return int
     */
    public function save(SuperCategoryModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('SuperCategory', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    public function update(SuperCategoryModel $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('SuperCategory', $data, "super_category_id={$data->getSuperCategoryId()}")
        );
        $statement->execute($this->toSqlValues($data));

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
}
