<?php

namespace Althingi\Model;

class CommitteeDocument implements ModelInterface
{
    private ?int $document_committee_id = null;
    private int $document_id;
    private int $assembly_id;
    private int $issue_id;
    private KindEnum $kind;
    private int $committee_id;
    private ?string $part = null;
    private ?string $name = null;

    public function getDocumentCommitteeId(): ?int
    {
        return $this->document_committee_id;
    }

    public function setDocumentCommitteeId(?int $document_committee_id): static
    {
        $this->document_committee_id = $document_committee_id;
        return $this;
    }

    public function getDocumentId(): int
    {
        return $this->document_id;
    }

    public function setDocumentId(int $document_id): static
    {
        $this->document_id = $document_id;
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

    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    public function setIssueId(int $issue_id): static
    {
        $this->issue_id = $issue_id;
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

    public function getCommitteeId(): int
    {
        return $this->committee_id;
    }

    public function setCommitteeId(int $committee_id): static
    {
        $this->committee_id = $committee_id;
        return $this;
    }

    public function getPart(): ?string
    {
        return $this->part;
    }

    public function setPart(?string $part): static
    {
        $this->part = $part;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function jsonSerialize(): mixed
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
            'kind' => $this->kind->value,
            'committee_id' => $this->committee_id,
            'part' => $this->part,
            'name' => $this->name,
        ];
    }
}
