<?php

namespace Althingi\Model;

class IssueLink implements ModelInterface
{
    private $from_assembly_id;
    private $from_issue_id;
    private $from_category;
    private $assembly_id;
    private $issue_id;
    private $category;
    private ?string $type = null;

    public function getFromAssemblyId(): int
    {
        return $this->from_assembly_id;
    }

    public function setFromAssemblyId(int $from_assembly_id): self
    {
        $this->from_assembly_id = $from_assembly_id;
        return $this;
    }

    public function getFromIssueId(): int
    {
        return $this->from_issue_id;
    }

    public function setFromIssueId(int $from_issue_id): self
    {
        $this->from_issue_id = $from_issue_id;
        return $this;
    }

    public function getFromCategory(): string
    {
        return $this->from_category;
    }

    public function setFromCategory(string $from_category): self
    {
        $this->from_category = $from_category;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): self
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    public function setIssueId(int $issue_id): self
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
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

    public function toArray(): array
    {
        return [
            'to_assembly_id' => $this->assembly_id,
            'to_issue_id' => $this->issue_id,
            'to_category' => $this->category,
            'from_assembly_id' => $this->from_assembly_id,
            'from_issue_id' => $this->from_issue_id,
            'from_category' => $this->from_category,
            'type' => $this->type,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
