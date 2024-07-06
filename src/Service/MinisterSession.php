<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableMinisterSessionPresenter;
use Althingi\Injector\{EventsAwareInterface, DatabaseAwareInterface};
use PDO;
use DateTime;
use Generator;

class MinisterSession implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $id): ?Model\MinisterSession
    {
        $statement = $this->getDriver()->prepare("select * from `MinisterSession` where minister_session_id = :id");
        $statement->execute(['id' => $id]);
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new Hydrator\MinisterSession())->hydrate($assembly, new Model\MinisterSession())
            : null;
    }

    /**
     * @return \Althingi\Model\MinisterSession[]
     */
    public function fetchByAssembly(int $assemblyId): array
    {
        $statement = $this->getDriver()->prepare("
            select * from MinisterSession where assembly_id = :assembly_id;
        ");
        $statement->execute(['assembly_id' => $assemblyId,]);
        $sittings = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($object) {
            return (new Hydrator\MinisterSession())->hydrate($object, new Model\MinisterSession());
        }, $sittings);
    }

    /**
     * @return \Althingi\Model\MinisterSession[]
     */
    public function fetchByCongressmanAssembly(int $assemblyId, int $congressmanId)
    {
        $statement = $this->getDriver()->prepare("
            select * from `MinisterSession`
                where assembly_id = :assembly_id and congressman_id = :congressman_id
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);
        $sittings = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($object) {
            return (new Hydrator\MinisterSession())->hydrate($object, new Model\MinisterSession());
        }, $sittings);
    }

    /**
     * @return \Althingi\Model\MinisterSession[]
     */
    public function fetchAllGenerator(?int $assemblyId = null): Generator
    {
        if ($assemblyId) {
            $statement = $this->getDriver()
                ->prepare(
                    'select * from MinisterSession where assembly_id = :assembly_id order by `minister_session_id`'
                );
            $statement->execute(['assembly_id' => $assemblyId]);
        } else {
            $statement = $this->getDriver()
                ->prepare('select * from MinisterSession order by `minister_session_id`');
            $statement->execute();
        }

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\MinisterSession())->hydrate($object, new Model\MinisterSession());
        }
        $statement->closeCursor();
        return null;
    }

    public function create(Model\MinisterSession $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('MinisterSession', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $id = $this->getDriver()->lastInsertId();
        $data->setMinisterSessionId($id);

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableMinisterSessionPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $id;
    }

    public function save(Model\MinisterSession $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('MinisterSession', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexableMinisterSessionPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexableMinisterSessionPresenter($data), ['rows' => $statement->rowCount()]),
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\MinisterSession $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('MinisterSession', $data, "minister_session_id={$data->getMinisterSessionId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableMinisterSessionPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    public function delete(int $id): int
    {
        $statement = $this->getDriver()->prepare(
            "delete from `MinisterSession` where minister_session_id = :minister_session_id"
        );
        $statement->execute(['minister_session_id' => $id]);

        return $statement->rowCount();
    }

    /**
     * @return mixed
     */
    public function getIdentifier(int $assemblyId, int $ministryId, $congressmanId, DateTime $from)
    {
        $statement = $this->getDriver()->prepare('
            select `minister_session_id` from `MinisterSession`
            where `congressman_id` = :congressman_id and
                `ministry_id` = :ministry_id and
                `assembly_id` = :assembly_id and
                `from` = :from;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'ministry_id' => $ministryId,
            'assembly_id' => $assemblyId,
            'from' => $from->format('Y-m-d'),
        ]);
        return $statement->fetchColumn(0);
    }
}
