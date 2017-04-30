<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
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
