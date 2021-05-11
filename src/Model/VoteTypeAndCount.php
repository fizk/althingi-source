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
    public function setCount(int $count): self
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
    public function setVote(string $vote): self
    {
        $this->vote = $vote;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'count' => $this->count,
            'vote' => $this->vote,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
