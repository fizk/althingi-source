<?php

namespace Althingi\Model;

class VoteItemAndCount extends VoteItem
{
    private int $count;

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), ['count' => $this->count]);
    }
}
