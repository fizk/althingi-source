<?php

namespace Althingi\Model;

class VoteTypeAndCount implements ModelInterface
{
    /** @var int */
    private $count;

    /** @var string */
    private $vote;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return VoteTypeAndCount
     */
    public function setCount(int $count): VoteTypeAndCount
    {
        $this->count = $count;
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
     * @return VoteTypeAndCount
     */
    public function setVote(string $vote): VoteTypeAndCount
    {
        $this->vote = $vote;
        return $this;
    }

    public function toArray()
    {
        return [
            'count' => $this->count,
            'vote' => $this->vote,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
