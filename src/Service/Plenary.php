<?php

namespace Althingi\Service;

use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Presenters\IndexablePlenaryPresenter;
use Generator;
use PDO;

class Plenary implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $assemblyId, int $plenaryId): ? Model\Plenary
    {
        $statement = $this->getDriver()->prepare('
            select * from `Plenary` where assembly_id = :assembly_id and plenary_id = :plenary_id
        ');
        $statement->execute([
            'assembly_id' => $assemblyId,
            'plenary_id' => $plenaryId,
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\Plenary())->hydrate($object, new Model\Plenary())
            : null;
    }

    /**
     * @return \Althingi\Model\Plenary[]
     */
    public function fetchByAssembly(int $id, int $offset, int $size = null, string $order = 'desc'): array
    {
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';
        $statement = $this->getDriver()->prepare("
            select * from `Plenary` P where assembly_id = :id
            order by P.`from` {$order}
            limit {$offset}, {$size}
        ");
        $statement->execute(['id' => $id]);

        return array_map(function ($object) {
            return (new Hydrator\Plenary())->hydrate($object, new Model\Plenary());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function fetchAllGenerator(?int $assembly_id): Generator
    {
        $params = [
            'assembly_id' => $assembly_id,
        ];

        $filteredParams = array_filter($params, function ($value) {
            return $value !== null;
        });

        $statement = $this->getDriver()->prepare(
            $this->toSelectString('Plenary', $filteredParams, 'plenary_id')
        );
        $statement->execute($filteredParams);

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\Plenary)->hydrate($object, new Model\Plenary());
        }
        $statement->closeCursor();
        return null;
    }

    public function countByAssembly(int $id): int
    {
        $statement = $this->getDriver()->prepare("
            select count(*) from `Plenary` P where assembly_id = :id
        ");
        $statement->execute(['id' => $id]);
        return (int) $statement->fetchColumn(0);
    }

    public function create(Model\Plenary $data)
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('Plenary', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexablePlenaryPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\Plenary $data)
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('Plenary', $data)
        );
        $statement->execute($this->toSqlValues($data));

        switch ($statement->rowCount()) {
            case 1:
                $this->getEventDispatcher()->dispatch(
                    new AddEvent(new IndexablePlenaryPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
            case 0:
            case 2:
                $this->getEventDispatcher()->dispatch(
                    new UpdateEvent(new IndexablePlenaryPresenter($data), ['rows' => $statement->rowCount()])
                );
                break;
        }
        return $statement->rowCount();
    }

    public function update(Model\Plenary $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString(
                'Plenary',
                $data,
                "plenary_id = {$data->getPlenaryId()} and assembly_id = {$data->getAssemblyId()}"
            )
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexablePlenaryPresenter($data), ['rows' => $statement->rowCount()])
        );

        return $statement->rowCount();
    }
}
