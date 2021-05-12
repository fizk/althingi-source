<?php

namespace Althingi\Model;

class CommitteeDocument implements ModelInterface
{
    private ?int $document_committee_id = null;
    private int $document_id;
    private int $assembly_id;
    private int $issue_id;
    private string $category;
    private int $committee_id;
    private ?string $part = null;
    private ?string $name = null;

    public function getDocumentCommitteeId(): ?int
    {
        return $this->document_committee_id;
    }

    public function setDocumentCommitteeId(?int $document_committee_id): self
    {
        $this->document_committee_id = $document_committee_id;
        return $this;
    }

    public function getDocumentId(): int
    {
        return $this->document_id;
    }

    public function setDocumentId(int $document_id): self
    {
        $this->document_id = $document_id;
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

    public function getCommitteeId(): int
    {
        return $this->committee_id;
    }

    public function setCommitteeId(int $committee_id): self
    {
        $this->committee_id = $committee_id;
        return $this;
    }

    public function getPart(): ?string
    {
        return $this->part;
    }

    public function setPart(?string $part): self
    {
        $this->part = $part;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'document_committee_id' => $this->document_committee_id,
            'document_id' => $this->document_id,
            'assembly_id' => $this->assembly_id,
            'issue_id' => $this->issue_id,
            'category' => $this->category,
            'committee_id' => $this->committee_id,
            'part' => $this->part,
            'name' => $this->name,
        ];
    }
}
