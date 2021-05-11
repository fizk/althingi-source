<?php

namespace Althingi\Model;

class IssueCategoryAndTime implements ModelInterface
{
    /** @var  int */
    private $category_id;

    /** @var  int */
    private $super_category_id;

    /** @var  string */
    private $title;

    /** @var  int */
    private $time;

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    /**
     * @param int $category_id
     * @return IssueCategoryAndTime
     */
    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSuperCategoryId(): int
    {
        return $this->super_category_id;
    }

    /**
     * @param int $super_category_id
     * @return IssueCategoryAndTime
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
     * @return IssueCategoryAndTime
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
     * @return IssueCategoryAndTime
     */
    public function setTime(int $time): self
    {
        $this->time = $time;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'super_category_id' => $this->super_category_id,
            'title' => $this->title,
            'time' => $this->time,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
