<?php

namespace Althingi\Model;

class VoteTypeAndCount implements ModelInterface
{
    private int $count;
    private string $vote;

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;
        return $this;
    }

    public function getVote(): string
    {
        return $this->vote;
    }

    public function setVote(string $vote): static
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

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
