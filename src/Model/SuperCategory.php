<?php

namespace Althingi\Model;

class SuperCategory implements ModelInterface
{
    private $super_category_id;
    private ?string $title = null;

    public function getSuperCategoryId(): int
    {
        return $this->super_category_id;
    }

    public function setSuperCategoryId(int $super_category_id): self
    {
        $this->super_category_id = $super_category_id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'super_category_id' => $this->super_category_id,
            'title' => $this->title,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
