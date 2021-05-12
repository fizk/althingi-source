<?php

namespace Althingi\Model;

class IssueCategoryAndTime implements ModelInterface
{
    private int $category_id;
    private int $super_category_id;
    private string $title;
    private int $time;

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;
        return $this;
    }

    public function getSuperCategoryId(): int
    {
        return $this->super_category_id;
    }

    public function setSuperCategoryId(int $super_category_id): self
    {
        $this->super_category_id = $super_category_id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }

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
