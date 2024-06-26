<?php

namespace Althingi\Model;

class Category implements ModelInterface
{
    private int $category_id;
    private int $super_category_id;
    private ?string $title = null;
    private ?string $description = null;

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function setCategoryId(int $category_id): static
    {
        $this->category_id = $category_id;
        return $this;
    }

    public function getSuperCategoryId(): int
    {
        return $this->super_category_id;
    }

    public function setSuperCategoryId(int $super_category_id): static
    {
        $this->super_category_id = $super_category_id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'super_category_id' => $this->super_category_id,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
