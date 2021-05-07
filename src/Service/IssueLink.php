<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableIssueLinkPresenter;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use PDO;

class IssueLink implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    /**
     * @return \Althingi\Model\Issue
     */
    public function fetchAll(int $assemblyId, int $issueId, string $category = 'A'): array
    {
        $statement = $this->getDriver()
            ->prepare("
                select I.* from IssueLink IL
                    join Issue I on (
                          IL.to_assembly_id = I.assembly_id
                          and IL.to_issue_id = I.issue_id
                          and IL.to_category = I.category
                      )
                where IL.from_assembly_id = :assembly_id
                  and IL.from_issue_id = :issue_id
                  and IL.from_category = :category;
            ");
        $statement->execute(['assembly_id' => $assemblyId, 'issue_id' => $issueId, 'category' => $category]);

        return array_map(function ($issue) {
            return (new Hydrator\Issue())->hydrate($issue, new Model\Issue());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(Model\IssueLink $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('IssueLink', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableIssueLinkPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\IssueLink $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('IssueLink', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableIssueLinkPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableIssueLinkPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\IssueLink $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'IssueLink',
                $data,
                "to_assembly_id={$data->getAssemblyId()} and" .
                "to_issue_id={$data->getIssueId()} and" .
                "to_category={$data->getCategory()} and" .
                "from_assembly_id={$data->getFromAssemblyId()} and" .
                "from_issue_id={$data->getFromIssueId()} and" .
                "from_category={$data->getFromCategory()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableIssueLinkPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }
}
