<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
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
