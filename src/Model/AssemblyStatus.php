<?php

namespace Althingi\Model;

class AssemblyStatus implements ModelInterface
{
    private ?int $count = null;
    private ?string $type = null;
    private ?string $type_name = null;
    private ?string $type_subname = null;
    private ?string $status = null;
    private ?string $category = null;

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): self
    {
        $this->count = $count;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function setTypeName(?string $type_name): self
    {
        $this->type_name = $type_name;
        return $this;
    }

    public function getTypeSubname(): ?string
    {
        return $this->type_subname;
    }

    public function setTypeSubname(?string $type_subname): self
    {
        $this->type_subname = $type_subname;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'count' => $this->count,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'type_subname' => $this->type_subname,
            'status' => $this->status,
            'category' => $this->category,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
