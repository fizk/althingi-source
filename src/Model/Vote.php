<?php

namespace Althingi\Model;

use DateTime;

class Vote implements ModelInterface
{
    private $vote_id;
    private $issue_id;
    private KindEnum $kind;
    private $assembly_id;
    private ?int $document_id = null;
    private ?DateTime $date = null;
    private ?string $type = null;
    private ?string $outcome = null;
    private ?string $method = null;
    private ?int $yes = null;
    private ?int $no = null;
    private ?int $inaction = null;
    private ?string $committee_to = null;

    public function getVoteId(): int
    {
        return $this->vote_id;
    }

    public function setVoteId(int $vote_id): static
    {
        $this->vote_id = $vote_id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getOutcome(): ?string
    {
        return $this->outcome;
    }

    public function setOutcome(?string $outcome): static
    {
        $this->outcome = $outcome;
        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): static
    {
        $this->method = $method;
        return $this;
    }

    public function getYes(): ?int
    {
        return $this->yes;
    }

    public function setYes(?int $yes): static
    {
        $this->yes = $yes;
        return $this;
    }

    public function getNo(): ?int
    {
        return $this->no;
    }

    public function setNo(?int $no): static
    {
        $this->no = $no;
        return $this;
    }

    public function getInaction(): ?int
    {
        return $this->inaction;
    }

    public function setInaction(?int $inaction): static
    {
        $this->inaction = $inaction;
        return $this;
    }

    public function getCommitteeTo(): ?string
    {
        return $this->committee_to;
    }

    public function setCommitteeTo(?string $committee_to): static
    {
        $this->committee_to = $committee_to;
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
            'vote_id' => $this->vote_id,
            'issue_id' => $this->issue_id,
            'kind' => $this->kind->value,
            'assembly_id' => $this->assembly_id,
            'document_id' => $this->document_id,
            'date' => $this->date?->format('Y-m-d H:m:s'),
            'type' => $this->type,
            'outcome' => $this->outcome,
            'method' => $this->method,
            'yes' => $this->yes,
            'no' => $this->no,
            'inaction' => $this->inaction,
            'committee_to' => $this->committee_to,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
