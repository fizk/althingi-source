<?php

namespace Althingi\Service;

use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Presenters\IndexableParliamentarySessionPresenter;
use Generator;
use PDO;

class ParliamentarySession implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $assemblyId, int $parliamentarySessionId): ?Model\ParliamentarySession
    {
        $statement = $this->getDriver()->prepare('
            select * from `ParliamentarySession`
            where assembly_id = :assembly_id
                and parliamentary_session_id = :parliamentary_session_id
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'parliamentary_session_id' => $parliamentarySessionId,
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\ParliamentarySession())->hydrate($object, new Model\ParliamentarySession())
            : null;
    }

    /**
     * @return \Althingi\Model\ParliamentarySession[]
     */
    public function fetchByAssembly(int $id, int $offset, int $size = null, string $order = 'desc'): array
    {
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';
        $statement = $this->getDriver()->prepare("
            select * from `ParliamentarySession` P where assembly_id = :id
            order by P.`from` {$order}
            limit {$offset}, {$size}
        ");
        $statement->execute(['id' => $id]);

        return array_map(function ($object) {
            return (new Hydrator\ParliamentarySession())->hydrate($object, new Model\ParliamentarySession());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @return \Althingi\Model\ParliamentarySession[]
     */
    public function fetchAllGenerator(?int $assembly_id): Generator
    {
        $params = [
            'assembly_id' => $assembly_id,
        ];

        $filteredParams = array_filter($params, function ($value) {
            return $value !== null;
        });

        $statement = $this->getDriver()->prepare(
            $this->toSelectString('ParliamentarySession', $filteredParams, 'parliamentary_session_id')
        );
        $statement->execute($filteredParams);

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\ParliamentarySession())->hydrate($object, new Model\ParliamentarySession());
        }
        $statement->closeCursor();
        return null;
    }

    public function countByAssembly(int $id): int
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `ParliamentarySession` P where assembly_id = :id
        ");
        $statement->execute(['id' => $id]);
        return (int) $statement->fetchColumn(0);
    }

    public function create(Model\ParliamentarySession $data): string|false
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('ParliamentarySession', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableParliamentarySessionPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\ParliamentarySession $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('ParliamentarySession', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(
                        new IndexableParliamentarySessionPresenter($data),
                        ['rows' => $statement->rowCount()]
                    )
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(
                        new IndexableParliamentarySessionPresenter($data),
                        ['rows' => $statement->rowCount()]
                    )
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\ParliamentarySession $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'ParliamentarySession',
                $data,
                "parliamentary_session_id = {$data->getParliamentarySessionId()}
                and assembly_id = {$data->getAssemblyId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableParliamentarySessionPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $statement->rowCount();
    }
}
