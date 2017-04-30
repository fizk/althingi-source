<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class VoteItem implements ModelInterface
{
    /** @var int */
    private $vote_id;

    /** @var int */
    private $congressman_id;

    /** @var string */
    private $vote;

    /** @var int */
    private $vote_item_id;

    /**
     * @return int
     */
    public function getVoteId(): int
    {
        return $this->vote_id;
    }

    /**
     * @param int $vote_id
     * @return VoteItem
     */
    public function setVoteId(int $vote_id): VoteItem
    {
        $this->vote_id = $vote_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    /**
     * @param int $congressman_id
     * @return VoteItem
     */
    public function setCongressmanId(int $congressman_id): VoteItem
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getVote(): string
    {
        return $this->vote;
    }

    /**
     * @param string $vote
     * @return VoteItem
     */
    public function setVote(string $vote): VoteItem
    {
        $this->vote = $vote;
        return $this;
    }

    /**
     * @return int
     */
    public function getVoteItemId(): int
    {
        return $this->vote_item_id;
    }

    /**
     * @param int $vote_item_id
     * @return VoteItem
     */
    public function setVoteItemId(int $vote_item_id): VoteItem
    {
        $this->vote_item_id = $vote_item_id;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'vote_id' => $this->vote_id,
            'congressman_id' => $this->congressman_id,
            'vote' => $this->vote,
            'vote_item_id' => $this->vote_item_id,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
