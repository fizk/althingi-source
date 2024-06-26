<?php

namespace Althingi\Model;

class IssueTypeAndStatus implements ModelInterface
{
    private ?string $type = null;
    private ?string $typeName = null;
    private ?string $typeSubName = null;
    private int $count = 0;
    /** @var \Althingi\Model\IssueTypeStatus[] */
    private array $status = [];

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    public function setTypeName(?string $typeName): static
    {
        $this->typeName = $typeName;
        return $this;
    }

    public function getTypeSubName(): ?string
    {
        return $this->typeSubName;
    }

    public function setTypeSubName(?string $typeSubName): static
    {
        $this->typeSubName = $typeSubName;
        return $this;
    }

    /**
     * @return IssueTypeStatus[]
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param IssueTypeStatus[] $status
     */
    public function setStatus(array $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function addStatus(IssueTypeStatus $status): static
    {
        $this->status[] = $status;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;
        return $this;
    }

    public function addCount(int $count): static
    {
        $this->count += $count;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'typeName' => $this->typeName,
            'typeSubName' => $this->typeSubName,
            'status' => $this->status,
            'count' => $this->count,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
