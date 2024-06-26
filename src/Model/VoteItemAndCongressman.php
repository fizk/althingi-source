<?php

namespace Althingi\Model;

class VoteItemAndCongressman implements ModelInterface
{
    private VoteItem $voteItem;
    private CongressmanPartyProperties $congressman;

    public function getVoteItem(): VoteItem
    {
        return $this->voteItem;
    }

    public function setVoteItem(VoteItem $voteItem): static
    {
        $this->voteItem = $voteItem;
        return $this;
    }

    public function getCongressman(): CongressmanPartyProperties
    {
        return $this->congressman;
    }

    public function setCongressman(CongressmanPartyProperties $congressman): static
    {
        $this->congressman = $congressman;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->voteItem->toArray(),
            ['congressman' => $this->congressman->toArray()]
        );
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
