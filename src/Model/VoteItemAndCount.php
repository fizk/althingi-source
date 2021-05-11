<?php

namespace Althingi\Model;

class VoteItemAndCount extends VoteItem
{
    /** @var int */
    private $count;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return VoteItemAndCount
     */
    public function setCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), ['count' => $this->count]);
    }
}
