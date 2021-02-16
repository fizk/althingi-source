<?php

namespace Althingi\Service;

use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Injector\EventsAwareInterface;
use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use Althingi\Presenters\IndexablePresidentPresenter;
use PDO;
use DateTime;

/**
 * Class President
 * @package Althingi\Service
 */
class President implements DatabaseAwareInterface, EventsAwareInterface
{
    use DatabaseService;
    use EventService;

    public function get(int $id): ? Model\President
    {
        $statement = $this->getDriver()->prepare(
            "select *
                from `President` P
                where P.`president_id` = :president_id;"
        );
        $statement->execute(['president_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\President())->hydrate($object, new Model\President())
            : null;
    }

    public function fetch(): array
    {
        $statement = $this->getDriver()->prepare(
            "select *
                from `President` P
                order by P.`president_id`"
        );
        $statement->execute();

        return array_map(function ($object) {
            return (new Hydrator\President())->hydrate($object, new Model\President());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $id
     * @return \Althingi\Model\PresidentCongressman|null
     */
    public function getWithCongressman(int $id): ? Model\PresidentCongressman
    {
        $statement = $this->getDriver()->prepare(
            "select P.`president_id`, P.`assembly_id`, P.`from`, P.`to`, P.`title`, P.`abbr`, C.*
                from `President` P
                join `Congressman` C on (P.`congressman_id` = C.`congressman_id`)
                where P.`president_id` = :president_id;"
        );
        $statement->execute(['president_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\PresidentCongressman())->hydrate($object, new Model\PresidentCongressman())
            : null;
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
     * @param DateTime $from
     * @param string $title
     * @return \Althingi\Model\PresidentCongressman|null
     */
    public function getByUnique(
        int $assemblyId,
        int $congressmanId,
        DateTime $from,
        string $title
    ): ? Model\PresidentCongressman {
        $statement = $this->getDriver()->prepare("
            select P.`president_id`, P.`assembly_id`, P.`from`, P.`to`, P.`title`, P.`abbr`, C.*
            from `President` P
            join `Congressman` C on (P.`congressman_id` = C.`congressman_id`)
            where P.`assembly_id` = :assembly_id
              and P.`congressman_id` = :congressman_id
              and P.`title` = :title
              and P.`from` = :from;
        ");
        $statement->execute([
            'assembly_id' => $assemblyId,
            'congressman_id' => $congressmanId,
            'title' => $title,
            'from' => $from->format('Y-m-d'),
        ]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\PresidentCongressman())->hydrate($object, new Model\PresidentCongressman())
            : null;
    }

    /**
     * @param \Althingi\Model\President $data
     * @return int
     */
    public function create(Model\President $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('President', $data)
        );
        $statement->execute($this->toSqlValues($data));

        $id = $this->getDriver()->lastInsertId();
        $data->setPresidentId($id);
        $this->getEventManager()
            ->trigger(
                AddEvent::class,
                new AddEvent(new IndexablePresidentPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

        return $id;
    }

    /**
     * @param \Althingi\Model\President | object $data
     * @return int
     */
    public function update(Model\President $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('President', $data, "president_id={$data->getPresidentId()}")
        );
        $statement->execute($this->toSqlValues($data));

        $this->getEventManager()
            ->trigger(
                UpdateEvent::class,
                new UpdateEvent(new IndexablePresidentPresenter($data)),
                ['rows' => $statement->rowCount()]
            );

        return $statement->rowCount();
    }
}
