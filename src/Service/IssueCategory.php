<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableIssueCategoryPresenter;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use Althingi\Model\KindEnum;
use Generator;
use PDO;

class IssueCategory implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $assemblyId, int $issueId, int $categoryId): ?Model\IssueCategory
    {
        $statement = $this->getDriver()->prepare('
            select * from `Category_has_Issue` C
            where C.`assembly_id` = :assembly_id
              and C.`issue_id` = :issue_id
              and C.`category_id` = :category_id
              and C.kind = :kind
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
            'category_id' => $categoryId,
            'kind' => KindEnum::A->value
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\IssueCategory())->hydrate($object, new Model\IssueCategory())
            : null;
    }

    /**
     * @return \Althingi\Model\IssueCategory[]
     */
    public function fetchByIssue(int $assemblyId, int $issueId): array
    {
        $statement = $this->getDriver()->prepare('
            select * from Category_has_Issue where issue_id = :issue_id and assembly_id = :assembly_id
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'issue_id' => $issueId,
        ]);

        return array_map(function ($object) {
            return (new Hydrator\IssueCategory())->hydrate($object, new Model\IssueCategory());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchAllGenerator(?int $assemblyId = null): Generator
    {
        if ($assemblyId) {
            $statement = $this->getDriver()
                ->prepare('select * from `Category_has_Issue` where `assembly_id` = :assembly_id');
            $statement->execute([
                'assembly_id' => $assemblyId
            ]);
        } else {
            $statement = $this->getDriver()
                ->prepare('select * from `Category_has_Issue` order by `assembly_id`');
            $statement->execute();
        }


        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\IssueCategory())->hydrate($object, new Model\IssueCategory());
        }
        $statement->closeCursor();
        return null;
    }

    public function create(Model\IssueCategory $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Category_has_Issue', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableIssueCategoryPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\IssueCategory $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Category_has_Issue', $data)
        );
        $statement->execute($this->toSqlValues($data));
        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableIssueCategoryPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableIssueCategoryPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\IssueCategory $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'Category_has_Issue',
                $data,
                "category_id={$data->getCategoryId()} " .
                "and issue_id={$data->getIssueId()} " .
                "and assembly_id={$data->getAssemblyId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableIssueCategoryPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    /**
     * @return \Althingi\Model\IssueCategoryAndTime[]
     */
    public function fetchFrequencyByAssemblyAndCongressman(
        int $assemblyId,
        int $congressmanId,
        ?array $kind = [KindEnum::A]
    ): array {
        $categories = count($kind) > 0
            ? 'and SP.kind in (' . implode(',', array_map(function (KindEnum $item) {
                return '"' . $item->value . '"';
            }, $kind)) . ')'
            : '';

        $statement = $this->getDriver()->prepare("
            select C.`category_id`, C.`super_category_id`, C.`title`, sum(`speech_sum`) as `time` from (
                select CI.*, TIME_TO_SEC(timediff(SP.`to`, SP.`from`)) as `speech_sum`
                from `Speech` SP
                join `Category_has_Issue` CI on (CI.`issue_id` = SP.`issue_id`)
                where SP.`assembly_id` = :assembly_id and SP.`congressman_id` = :congressman_id {$categories}
            ) as T
            join `Category` C on (C.`category_id` = T.`category_id`)
            group by T.`category_id`
            order by `time` desc;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);

        return array_map(function ($object) {
            return (new Hydrator\IssueCategoryAndTime())->hydrate($object, new Model\IssueCategoryAndTime());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }
}
