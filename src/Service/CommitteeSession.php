<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Injector\{DatabaseAwareInterface, EventsAwareInterface};
use Althingi\Presenters\IndexableCommitteeSessionPresenter;
use PDO;
use DateTime;
use Generator;
use PDOException;

class CommitteeSession implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $id): ?Model\CommitteeSession
    {
        $statement = $this->getDriver()->prepare(
            "select * from `CommitteeSession` where committee_session_id = :committee_session_id"
        );
        $statement->execute(['committee_session_id' => $id]);
        $object = $statement->fetch(PDO::FETCH_ASSOC);

        return $object
            ? (new Hydrator\CommitteeSession())->hydrate($object, new Model\CommitteeSession())
            : null;
    }

    /**
     * @return \Althingi\Model\CommitteeSession[]
     */
    public function fetchAllGenerator(
        ?int $assemblyId = null,
        ?int $congressmanId = null,
        ?int $committeeId = null
    ): Generator {
        $params = [
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
            'committee_id' => $committeeId,
        ];

        $filteredParams = array_filter($params, function ($value) {
            return $value !== null;
        });

        $statement = $this->getDriver()->prepare(
            $this->toSelectString('CommitteeSession', $filteredParams, 'committee_session_id')
        );
        $statement->execute($filteredParams);

        while (($object = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            yield (new Hydrator\CommitteeSession())->hydrate($object, new Model\CommitteeSession());
        }
        $statement->closeCursor();
        return null;
    }

    public function create(Model\CommitteeSession $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('CommitteeSession', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $id = $this->getDriver()->lastInsertId();
        $data->setCommitteeSessionId($id);

        $this->getEventDispatcher()->dispatch(
            new AddEvent(new IndexableCommitteeSessionPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $id;
    }

    public function update(Model\CommitteeSession $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('CommitteeSession', $data, "committee_session_id={$data->getCommitteeSessionId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventDispatcher()->dispatch(
            new UpdateEvent(new IndexableCommitteeSessionPresenter($data), ['rows' => $statement->rowCount()]),
        );

        return $statement->rowCount();
    }

    /**
     * @return \Althingi\Model\CommitteeSession[]
     */
    public function fetchByCongressman(int $congressmanId)
    {
        $statement = $this->getDriver()->prepare(
            "select * from `CommitteeSession` where congressman_id = :congressman_id order by `from`"
        );
        $statement->execute(['congressman_id' => $congressmanId]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($object) {
            return (new Hydrator\CommitteeSession())->hydrate($object, new Model\CommitteeSession());
        }, $result);
    }

    public function getIdentifier(int $congressmanId, int $committeeId, int $assemblyId, DateTime $from): int
    {
        $statement = $this->getDriver()->prepare('
            select `committee_session_id` from `CommitteeSession`
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
