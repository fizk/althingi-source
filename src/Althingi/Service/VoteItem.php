<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/03/2016
 * Time: 11:22 AM
 */

namespace Althingi\Service;

use Althingi\Lib\DatabaseAwareInterface;
use PDO;

class VoteItem implements DatabaseAwareInterface
{
    use DatabaseService;

    /**
     * @var \PDO
     */
    private $pdo;

    public function get($id)
    {
        $statement = $this->getDriver()->prepare(
            'select * from `VoteItem` where vote_item_id = :vote__item_id'
        );
        $statement->execute(['vote__item_id' => $id]);

        return $this->decorate($statement->fetchObject());
    }

    /**
     * Get all vote-items by vote-id
     *
     * @param $id
     * @return array
     */
    public function fetchByVote($id)
    {
        $statement = $this->getDriver()->prepare(
            'select * from `VoteItem` where vote_id = :vote_id'
        );
        $statement->execute(['vote_id' => $id]);
        return array_map([$this, 'decorate'], $statement->fetchAll());
    }

    /**
     * If you don't have the vote-item's unique ID, you can get an individual
     * vote-item by the vote-id and congressman-id, since that is unique.
     *
     * @param $voteId
     * @param $congressmanId
     * @return null
     */
    public function getByVote($voteId, $congressmanId)
    {
        $statement = $this->getDriver()->prepare(
            'select vi.*, v.assembly_id, v.issue_id from `VoteItem` vi
            join `Vote` v on (vi.vote_id = v.vote_id)
            where vi.`vote_id` = :vote_id and vi.`congressman_id` = :congressman_id;'
        );
        $statement->execute(['vote_id' => $voteId, 'congressman_id' => $congressmanId]);
        return $this->decorate($statement->fetchObject());
    }

    /**
     * Create a vote-item.
     *
     * @todo should return the auto_increment value but currently
     *  the table doesn't have a auto_increment value.
     * @param $data
     * @return int
     */
    public function create($data)
    {
        $insertStatement = $this->getDriver()->prepare($this->insertString('VoteItem', $data));
        $insertStatement->execute($this->convert($data));
        return (int) $this->getDriver()->lastInsertId();
    }

    public function update($data)
    {
        $insertStatement = $this->getDriver()->prepare(
            $this->updateString('VoteItem', $data, "vote_item_id={$data->vote_item_id}")
        );
        $insertStatement->execute($this->convert($data));
        return (int) $insertStatement->columnCount();
    }

    /**
     * @param \PDO $pdo
     */
    public function setDriver(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getDriver()
    {
        return $this->pdo;
    }

    private function decorate($object)
    {
        if (!$object) {
            return null;
        }

        $object->vote_item_id = (int) $object->vote_item_id;
        $object->vote_id = (int) $object->vote_id;
        $object->congressman_id = (int) $object->congressman_id;
        return $object;
    }
}
