<?php

namespace Althingi\Model;

class IssueCategory implements ModelInterface
{
    private int $category_id;
    private int $issue_id;
    private int $assembly_id;
    private string $category;

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;
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

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): self
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category)
    {
        $this->category = $category;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'issue_id' => $this->issue_id,
            'assembly_id' => $this->assembly_id,
            'category' => $this->category,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
