<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Presenters\IndexableSessionPresenter;
use Althingi\Injector\{DatabaseAwareInterface, EventsAwareInterface};
use PDO;
use DateTime;
use Generator;

class Session implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $id): ?Model\Session
    {
        $statement = $this->getDriver()->prepare(
            "select * from `Session` where session_id = :session_id"
        );
        $statement->execute(['session_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\Session())->hydrate($object, new Model\Session())
            : null;
    }

    public function fetchAllGenerator(?int $assembly_id = null, ?int $congressman_id = null): Generator
    {
        $params = [
            'assembly_id' => $assembly_id,
            'congressman_id' => $congressman_id,
        ];

        $filteredParams = array_filter($params, function ($value) {
            return $value !== null;
        });

        $statement = $this->getDriver()->prepare(
            $this->toSelectString('Session', $filteredParams, 'session_id')
        );
        $statement->execute($filteredParams);

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Session())->hydrate($object, new Model\Session());
        }
        $statement->closeCursor();
        return null;
    }

    /**
     * @return \Althingi\Model\Session[]
     */
    public function fetchByCongressman(int $id): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Session` where congressman_id = :id
            order by `from` desc
        ");
        $statement->execute(['id' => $id]);

        return array_map(function ($object) {
            return (new Hydrator\Session())->hydrate($object, new Model\Session());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Session[]
     */
    public function fetchByAssembly(int $id): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Session` where assembly_id = :id
            order by `from` desc
        ");
        $statement->execute(['id' => $id]);

        return array_map(function ($object) {
            return (new Hydrator\Session())->hydrate($object, new Model\Session());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\Session[]
     */
    public function fetchByAssemblyAndCongressman(int $assemblyId, int $congressmanId): array
    {
        $statement = $this->getDriver()->prepare("
            select * from `Session` S where S.`congressman_id` = :congressman_id and S.`assembly_id` = :assembly_id
            order by `from` desc
        ");

        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
        ]);
        return array_map(function ($object) {
            return (new Hydrator\Session())->hydrate($object, new Model\Session());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getIdentifier(int $congressmanId, DateTime $from, string $type): int
    {
        $statement = $this->getDriver()->prepare('
            select `session_id` from `Session`
            where `congressman_id` = :congressman_id and `type` = :type and `from` = :from;
        ');
        $statement->execute([
            'congressman_id' => $congressmanId,
            'type' => $type,
            'from' => $from->format('Y-m-d'),
        ]);
        return $statement->fetchColumn(0);
    }

    public function create(Model\Session $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Session', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $id = $this->getDriver()->lastInsertId();
        $data->setSessionId($id);

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableSessionPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $id;
    }

    public function update(Model\Session $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('Session', $data, "session_id={$data->getSessionId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableSessionPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    public function delete(int $id)
    {
        $statement = $this->getDriver()->prepare("
            delete from `Session` where session_id = :id
        ");
        $statement->execute(['id' => $id]);
        return $statement->rowCount();
    }
}
