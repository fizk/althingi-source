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
     * Get one party.
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
