<?php

namespace Althingi\Model;

class IssueLink implements ModelInterface
{
    private $from_assembly_id;
    private $from_issue_id;
    private KindEnum $from_kind;
    private $assembly_id;
    private $issue_id;
    private KindEnum $kind;
    private ?string $type = null;

    public function getFromAssemblyId(): int
    {
        return $this->from_assembly_id;
    }

    public function setFromAssemblyId(int $from_assembly_id): static
    {
        $this->from_assembly_id = $from_assembly_id;
        return $this;
    }

    public function getFromIssueId(): int
    {
        return $this->from_issue_id;
    }

    public function setFromIssueId(int $from_issue_id): static
    {
        $this->from_issue_id = $from_issue_id;
        return $this;
    }

    public function getFromKind(): KindEnum
    {
        return $this->from_kind;
    }

    public function setFromKind(KindEnum $from_kind): static
    {
        $this->from_kind = $from_kind;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'to_assembly_id' => $this->assembly_id,
            'to_issue_id' => $this->issue_id,
            'to_kind' => $this->kind->value,
            'from_assembly_id' => $this->from_assembly_id,
            'from_issue_id' => $this->from_issue_id,
            'from_kind' => $this->from_kind->value,
            'type' => $this->type,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
