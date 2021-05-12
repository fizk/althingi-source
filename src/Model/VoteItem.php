<?php

namespace Althingi\Model;

class VoteItem implements ModelInterface
{
    private ?int $vote_id = null;
    private int $congressman_id;
    private string $vote;
    private ?int $vote_item_id = null;

    public function getVoteId(): ?int
    {
        return $this->vote_id;
    }

    public function setVoteId(?int $vote_id): self
    {
        $this->vote_id = $vote_id;
        return $this;
    }

    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    public function setCongressmanId(int $congressman_id): self
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    public function getVote(): string
    {
        return $this->vote;
    }

    public function setVote(string $vote): self
    {
        $this->vote = $vote;
        return $this;
    }

    public function getVoteItemId(): ?int
    {
        return $this->vote_item_id;
    }

    public function setVoteItemId(?int $vote_item_id): self
    {
        $this->vote_item_id = $vote_item_id;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'vote_id' => $this->vote_id,
            'congressman_id' => $this->congressman_id,
            'vote' => $this->vote,
            'vote_item_id' => $this->vote_item_id,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
