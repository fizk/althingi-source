<?php

namespace Althingi\Model;

class IssueSuperCategoryAndTime implements ModelInterface
{
    /** @var  int */
    private $super_category_id;

    /** @var  string */
    private $title;

    /** @var  int */
    private $time;

    /**
     * @return int
     */
    public function getSuperCategoryId(): int
    {
        return $this->super_category_id;
    }

    /**
     * @param int $super_category_id
     * @return IssueSuperCategoryAndTime
     */
    public function setSuperCategoryId(int $super_category_id): self
    {
        $this->super_category_id = $super_category_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return IssueSuperCategoryAndTime
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     * @return IssueSuperCategoryAndTime
     */
    public function setTime(int $time): self
    {
        $this->time = $time;
        return $this;
    }

    public function toArray()
    {
        return [
            'super_category_id' => $this->super_category_id,
            'title' => $this->title,
            'time' => $this->time,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
