<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Injector\{DatabaseAwareInterface, EventsAwareInterface};
use Althingi\Presenters\IndexableCommitteeSittingPresenter;
use PDO;
use DateTime;

class CommitteeSitting implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $id): ? Model\CommitteeSitting
    {
        $statement = $this->getDriver()->prepare(
            "select * from `CommitteeSitting` where committee_sitting_id = :committee_sitting_id"
        );
        $statement->execute(['committee_sitting_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\CommitteeSitting())->hydrate($object, new Model\CommitteeSitting())
            : null;
    }

    public function create(Model\CommitteeSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('CommitteeSitting', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $id = $this->getDriver()->lastInsertId();
        $data->setCommitteeSittingId($id);

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableCommitteeSittingPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $id;
    }

    public function update(Model\CommitteeSitting $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('CommitteeSitting', $data, "committee_sitting_id={$data->getCommitteeSittingId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableCommitteeSittingPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    /**
     * @return \Althingi\Model\CommitteeSitting[]
     */
    public function fetchByCongressman(int $congressmanId)
    {
        $statement = $this->getDriver()->prepare(
            "select * from `CommitteeSitting` where congressman_id = :congressman_id order by `from`"
        );
        $statement->execute(['congressman_id' => $congressmanId]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($object) {
            return (new Hydrator\CommitteeSitting())->hydrate($object, new Model\CommitteeSitting());
        }, $result);
    }

    public function getIdentifier(int $congressmanId, int $committeeId, int $assemblyId, DateTime $from): int
    {
        $statement = $this->getDriver()->prepare('
            select `committee_sitting_id` from `CommitteeSitting`
            where `congressman_id` = :congressman_id and
                `committee_id` = :committee_id and
                `assembly_id` = :assembly_id and
                `from` = :from;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'committee_id' => $committeeId,
            'assembly_id' => $assemblyId,
            'from' => $from->format('Y-m-d'),
        ]);
        return $statement->fetchColumn(0);
    }
}
