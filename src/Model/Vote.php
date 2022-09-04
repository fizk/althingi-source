<?php

namespace Althingi\Model;

use DateTime;

class Vote implements ModelInterface
{
    private $vote_id;
    private $issue_id;
    private $category;
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

    public function setVoteId(int $vote_id): self
    {
        $this->vote_id = $vote_id;
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

    public function getDocumentId(): ?int
    {
        return $this->document_id;
    }

    public function setDocumentId(?int $document_id): self
    {
        $this->document_id = $document_id;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): self
    {
        $this->date = $date;
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

    public function getOutcome(): ?string
    {
        return $this->outcome;
    }

    public function setOutcome(?string $outcome): self
    {
        $this->outcome = $outcome;
        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function getYes(): ?int
    {
        return $this->yes;
    }

    public function setYes(?int $yes): self
    {
        $this->yes = $yes;
        return $this;
    }

    public function getNo(): ?int
    {
        return $this->no;
    }

    public function setNo(?int $no): self
    {
        $this->no = $no;
        return $this;
    }

    public function getInaction(): ?int
    {
        return $this->inaction;
    }

    public function setInaction(?int $inaction): self
    {
        $this->inaction = $inaction;
        return $this;
    }

    public function getCommitteeTo(): ?string
    {
        return $this->committee_to;
    }

    public function setCommitteeTo(?string $committee_to): self
    {
        $this->committee_to = $committee_to;
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

    public function toArray(): array
    {
        return [
            'vote_id' => $this->vote_id,
            'issue_id' => $this->issue_id,
            'category' => $this->category,
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
