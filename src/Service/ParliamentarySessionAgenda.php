<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use Generator;
use PDO;

class ParliamentarySessionAgenda implements DatabaseAwareInterface
{
    use DatabaseService;

    public function get(int $assemblyId, int $parliamentarySessionId, int $itemId): ?Model\ParliamentarySessionAgenda
    {
        $statement = $this->getDriver()->prepare("
          select * from `ParliamentarySessionAgenda`
            where `assembly_id` = :assembly_id
            and `parliamentary_session_id` = :parliamentary_session_id
            and `item_id` = :item_id;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'parliamentary_session_id' => $parliamentarySessionId,
            'item_id' => $itemId,
        ]);
        $assembly = $statement->fetch(PDO::FETCH_ASSOC);

        return $assembly
            ? (new Hydrator\ParliamentarySessionAgenda())->hydrate($assembly, new Model\ParliamentarySessionAgenda())
            : null;
    }

    /**
     * @return Model\ParliamentarySessionAgenda[]
     */
    public function fetch(int $assemblyId, int $parliamentarySessionId): ?array
    {
        $statement = $this->getDriver()->prepare("
          select * from ParliamentarySessionAgenda PA
            where PA.`assembly_id` = :assembly_id
            and PA.`parliamentary_session_id` = :parliamentary_session_id
            order by PA.`item_id`;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'parliamentary_session_id' => $parliamentarySessionId,
        ]);

        return array_map(function ($item) {
            return (new Hydrator\ParliamentarySessionAgenda())->hydrate($item, new Model\ParliamentarySessionAgenda());
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
            $this->toSelectString('ParliamentarySessionAgenda', $filteredParams, 'item_id')
        );
        $statement->execute($filteredParams);

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\ParliamentarySessionAgenda())->hydrate($object, new Model\ParliamentarySessionAgenda());
        }
        $statement->closeCursor();
        return null;
    }

    public function create(Model\ParliamentarySessionAgenda $data)
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('ParliamentarySessionAgenda', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    public function save(Model\ParliamentarySessionAgenda $data)
    {
        $statement = $this->getDriver()->prepare(
            $this->toSaveString('ParliamentarySessionAgenda', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }
}
