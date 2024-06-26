<?php

namespace Althingi\Model;

class IssueCategory implements ModelInterface
{
    private int $category_id;
    private int $issue_id;
    private int $assembly_id;
    private KindEnum $kind;

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function setCategoryId(int $category_id): static
    {
        $this->category_id = $category_id;
        return $this;
    }

    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    public function setIssueId(int $issue_id): static
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): static
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getKind(): KindEnum
    {
        return $this->kind;
    }

    public function setKind(KindEnum $kind): static
    {
        $this->kind = $kind;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'issue_id' => $this->issue_id,
            'assembly_id' => $this->assembly_id,
            'kind' => $this->kind->value,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
