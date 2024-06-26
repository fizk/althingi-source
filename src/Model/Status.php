<?php

namespace Althingi\Model;

use DateTime;

class Status implements ModelInterface
{
    private int $assembly_id;
    private int $issue_id;
    private ?int $committee_id = null;
    private ?string $speech_id = null;
    private ?int $document_id = null;
    private ?string $committee_name = null;
    private ?DateTime $date = null;
    private ?string $title = null;
    private string $type;
    private int $value = 0;
    private bool $completed = false;

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

    public function getCommitteeId(): ?int
    {
        return $this->committee_id;
    }

    public function setCommitteeId(?int $committee_id): static
    {
        $this->committee_id = $committee_id;
        return $this;
    }

    public function getSpeechId(): ?string
    {
        return $this->speech_id;
    }

    public function setSpeechId(?string $speech_id): static
    {
        $this->speech_id = $speech_id;
        return $this;
    }

    public function getDocumentId(): ?int
    {
        return $this->document_id;
    }

    public function setDocumentId(?int $document_id): static
    {
        $this->document_id = $document_id;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getCommitteeName(): ?string
    {
        return $this->committee_name;
    }

    public function setCommitteeName(?string $committee_name): static
    {
        $this->committee_name = $committee_name;
        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $complete): static
    {
        $this->completed = $complete;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'assembly_id' => $this->assembly_id,
            'issue_id' => $this->issue_id,
            'committee_id' => $this->committee_id,
            'speech_id' => $this->speech_id,
            'document_id' => $this->document_id,
            'date' => $this->date?->format('Y-m-d H:i:s'),
            'title' => $this->title,
            'type' => $this->type,
            'committee_name' => $this->committee_name,
            'completed' => $this->completed,
            'value' => $this->value,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
