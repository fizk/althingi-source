<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use PDO;

class SuperCategory implements DatabaseAwareInterface
{
    use DatabaseService;

    public function get(int $id): ? Model\SuperCategory
    {
        $statement = $this->getDriver()->prepare('
            select * from `SuperCategory` where super_category_id = :super_category_id
        ');
        $statement->execute(['super_category_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\SuperCategory())->hydrate($object, new Model\SuperCategory())
            : null;
    }

    /**
     * @return \Althingi\Model\SuperCategory[]
     */
    public function fetch(): array
    {
        $statement = $this->getDriver()->prepare('
            select * from `SuperCategory`
        ');
        $statement->execute();

        return array_map(function ($object) {
            return (new Hydrator\SuperCategory())->hydrate($object, new Model\SuperCategory());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\SuperCategory[]
     */
    public function fetchByIssue(int $assemblyId, int $issueId, string$category = 'A'): array
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
            return (new Hydrator\SuperCategory())->hydrate($object, new Model\SuperCategory());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(Model\SuperCategory $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('SuperCategory', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\SuperCategory $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('SuperCategory', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    public function update(Model\SuperCategory $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('SuperCategory', $data, "super_category_id={$data->getSuperCategoryId()}")
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }
}
