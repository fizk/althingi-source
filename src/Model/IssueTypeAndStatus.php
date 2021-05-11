<?php

namespace Althingi\Model;

class IssueTypeAndStatus implements ModelInterface
{
    /** @var  string */
    private $type;

    /** @var  string */
    private $typeName;

    /** @var  string */
    private $typeSubName;

    /** @var int */
    private $count = 0;

    /** @var \Althingi\Model\IssueTypeStatus[] */
    private $status = [];

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return IssueTypeAndStatus
     */
    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    /**
     * @param string $typeName
     * @return IssueTypeAndStatus
     */
    public function setTypeName(?string $typeName): self
    {
        $this->typeName = $typeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeSubName(): ?string
    {
        return $this->typeSubName;
    }

    /**
     * @param string $typeSubName
     * @return IssueTypeAndStatus
     */
    public function setTypeSubName(?string $typeSubName): self
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
     * @return IssueTypeAndStatus
     */
    public function setStatus(array $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function addStatus(IssueTypeStatus $status): self
    {
        $this->status[] = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return IssueTypeAndStatus
     */
    public function setCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }

    public function addCount(int $count): self
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
