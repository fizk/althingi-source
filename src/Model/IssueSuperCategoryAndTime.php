<?php

namespace Althingi\Model;

class IssueSuperCategoryAndTime implements ModelInterface
{
    private int $super_category_id;
    private string $title;
    private int $time;

    public function getSuperCategoryId(): int
    {
        return $this->super_category_id;
    }

    public function setSuperCategoryId(int $super_category_id): static
    {
        $this->super_category_id = $super_category_id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime(int $time): static
    {
        $this->time = $time;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'super_category_id' => $this->super_category_id,
            'title' => $this->title,
            'time' => $this->time,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
