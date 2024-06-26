<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Model\KindEnum;
use Generator;
use PDO;

class Category implements DatabaseAwareInterface
{
    use DatabaseService;

    public function get(int $id): ?Model\Category
    {
        $statement = $this->getDriver()->prepare('
            select * from `Category` where category_id = :category_id
        ');
        $statement->execute(['category_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\Category())->hydrate($object, new Model\Category())
            : null;
    }

    public function fetch(int $superId): ?array
    {
        $statement = $this->getDriver()->prepare('
            select * from `Category` where super_category_id = :super_category_id
        ');
        $statement->execute(['super_category_id' => $superId]);

        return array_map(function ($object) {
            return (new Hydrator\Category())->hydrate($object, new Model\Category());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchAllGenerator(): Generator
    {
        $statement = $this->getDriver()
            ->prepare('select * from `Category` order by `category_id`');
        $statement->execute();

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Category())->hydrate($object, new Model\Category());
        }
        $statement->closeCursor();
        return null;
    }

    /**
     * @return \Althingi\Model\CategoryAndCount[]
     */
    public function fetchByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare('
            select count(*) as `count` , C.* from `Issue` I
            join `Category_has_Issue` CI on (CI.`issue_id` = I.`issue_id` and CI.`kind` = I.`kind`)
            join `Category` C on (C.`category_id` = CI.`category_id` and CI.assembly_id = :assembly_id)
            where I.`assembly_id` = :assembly_id
            group by CI.`category_id`
            order by `count` desc;
        ');
        $statement->execute(['assembly_id' => $assemblyId]);

        return array_map(function ($object) {
            return (new Hydrator\CategoryAndCount())->hydrate($object, new Model\CategoryAndCount());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Category[]
     */
    public function fetchByAssemblyAndIssue(int $assemblyId, int $issueId): array
    {
        $statement = $this->getDriver()->prepare('
            select C.* from `Category_has_Issue` CI
            join `Category` C on (C.`category_id` = CI.`category_id`)
            where CI.`assembly_id` = :assembly_id
              and CI.`issue_id` = :issue_id
              and CI.kind = :kind;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'kind' => KindEnum::A->value
        ]);
        return array_map(function ($object) {
            return (new Hydrator\Category())->hydrate($object, new Model\Category());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchByAssemblyIssueAndCategory(int $assemblyId, int $issueId, int $categoryId): ?Model\Category
    {
        $statement = $this->getDriver()->prepare('
            select C.* from `Category_has_Issue` CI
            join `Category` C on (C.`category_id` = CI.`category_id`)
            where CI.`assembly_id` = :assembly_id
              and CI.`issue_id` = :issue_id
              and CI.`category_id` = :category_id
              and CI.kind = :kind;
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'category_id' => $categoryId,
            'kind' => KindEnum::A->value
        ]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\Category())->hydrate($object, new Model\Category())
            : null;
    }

    public function create(Model\Category $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Category', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\Category $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Category', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    public function update(Model\Category $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Category', $data, "category_id={$data->getCategoryId()}")
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }
}
