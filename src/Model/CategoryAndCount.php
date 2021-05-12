<?php

namespace Althingi\Model;

class CategoryAndCount extends Category
{
    private ?int $count = null;

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): self
    {
        $this->count = $count;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), ['count' => $this->count]);
    }
}
