<?php

namespace Althingi\Model;

class IssueTypeStatus implements ModelInterface
{
    private ?int $count = null;
    private ?string $status = null;

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): self
    {
        $this->count = $count;
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

    public function toArray(): array
    {
        return [
            'count' => $this->count,
            'status' => $this->status,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
