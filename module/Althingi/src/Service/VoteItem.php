<?php

namespace Althingi\Service;

use Althingi\Model;
use Althingi\Hydrator;
use Althingi\Injector\DatabaseAwareInterface;
use PDO;

class VoteItem implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @param int $id
     * @return \Althingi\Model\VoteItem|null
     */
    public function get(int $id): ? Model\VoteItem
    {
        $statement = $this->getDriver()->prepare(
            'select * from `VoteItem` where vote_item_id = :vote_item_id'
        );
        $statement->execute(['vote_item_id' => $id]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\VoteItem())->hydrate($object, new Model\VoteItem())
            : null;
    }

    /**
     * Get all vote-items by vote-id
     *
     * @param int $id
     * @return \Althingi\Model\VoteItem[]
     */
    public function fetchByVote(int $id): array
    {
        $statement = $this->getDriver()->prepare(
            'select * from `VoteItem` where vote_id = :vote_id'
        );
        $statement->execute(['vote_id' => $id]);

        return array_map(function ($object) {
            return (new Hydrator\VoteItem())->hydrate($object, new Model\VoteItem());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * If you don't have the vote-item's unique ID, you can get an individual
     * vote-item by the vote-id and congressman-id, since that is unique.
     *
     * @param int $voteId
     * @param int $congressmanId
     * @return \Althingi\Model\VoteItemAndAssemblyIssue|null
     */
    public function getByVote(int $voteId, int $congressmanId): ? Model\VoteItemAndAssemblyIssue
    {
        $statement = $this->getDriver()->prepare(
            'select vi.*, v.assembly_id, v.issue_id from `VoteItem` vi
            join `Vote` v on (vi.vote_id = v.vote_id)
            where vi.`vote_id` = :vote_id and vi.`congressman_id` = :congressman_id;'
        );
        $statement->execute(['vote_id' => $voteId, 'congressman_id' => $congressmanId]);

        $object = $statement->fetch(PDO::FETCH_ASSOC);
        return $object
            ? (new Hydrator\VoteItemAndAssemblyIssue())->hydrate($object, new Model\VoteItemAndAssemblyIssue())
            : null;
    }

    /**
     * @param int $assemblyId
     * @param int $congressmanId
     * @return \Althingi\Model\VoteItem[]
     */
    public function fetchVoteByAssemblyAndCongressmanAndCategory(int $assemblyId, int $congressmanId): array
    {
        $statement = $this->getDriver()->prepare('
        select CI.`category_id`, C.`title`, VI.vote_item_id, V.vote_id, 
              VI.`congressman_id`, V.`assembly_id`, VI.`vote`, count(VI.`vote`) as `count` 
        from `Vote` V
            join `VoteItem` VI on (VI.`vote_id` = V.`vote_id`)
            join `Category_has_Issue` CI on (CI.`assembly_id` = V.`assembly_id` and V.`issue_id` = CI.`issue_id`)
            join `Category` C on (C.`category_id` = CI.`category_id`)
        where V.`assembly_id` = :assembly_id and VI.`congressman_id` = :congressman_id
        group by CI.`category_id`, VI.`vote`
        order by C.`category_id`;
        ');
        $statement->execute(['assembly_id' => $assemblyId, 'congressman_id' => $congressmanId]);

        return array_map(function ($object) {
            return (new Hydrator\VoteItemAndCount())->hydrate($object, new Model\VoteItemAndCount());
        }, $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Create a vote-item.
     *
     * @todo should return the auto_increment value but currently
     *  the table doesn't have a auto_increment value.
     * @param \Althingi\Model\VoteItem $data
     * @return int
     */
    public function create(Model\VoteItem $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toInsertString('VoteItem', $data)
        );
        $statement->execute($this->toSqlValues($data));

        return $this->getDriver()->lastInsertId();
    }

    /**
     * @param \Althingi\Model\VoteItem | object $data
     * @return int
     */
    public function update(Model\VoteItem $data): int
    {
        $statement = $this->getDriver()->prepare(
            $this->toUpdateString('VoteItem', $data, "vote_item_id={$data->getVoteItemId()}")
        );
        $statement->execute($this->toSqlValues($data));

        return $statement->rowCount();
    }

    /**
     * @param \PDO $pdo
     * @return $this
     */
    public function setDriver(PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }

    /**
     * @return \PDO
     */
    public function getDriver()
    {
        return $this->pdo;
    }
}
