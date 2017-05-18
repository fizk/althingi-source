<?php

namespace Althingi\Model;

class CategoryAndCount extends Category
{
    /** @var  int */
    private $count;

    /**
     * @return int
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return CategoryAndCount
     */
    public function setCount(int $count = null): CategoryAndCount
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), ['count' => $this->count]);
    }
}
