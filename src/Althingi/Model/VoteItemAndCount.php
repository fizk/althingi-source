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
    public function setCount(int $count): VoteItemAndCount
    {
        $this->count = $count;
        return $this;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), ['count' => $this->count]);
    }
}
