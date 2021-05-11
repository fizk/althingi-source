<?php

namespace Althingi\Model;

use DateTime;

class Status implements ModelInterface
{

    /** @var  int */
    private $assembly_id;

    /** @var  int */
    private $issue_id;

    /** @var  int */
    private $committee_id;

    /** @var  string */
    private $speech_id;

    /** @var  int */
    private $document_id;

    /** @var  string */
    private $committee_name;

    /** @var  \DateTime */
    private $date;

    /** @var  string */
    private $title;

    /** @var  string */
    private $type;

    /** @var  int */
    private $value = 0;

    /** @var  bool */
    private $completed = false;

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    /**
     * @param int $assembly_id
     * @return Status
     */
    public function setAssemblyId(int $assembly_id): self
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    /**
     * @param int $issue_id
     * @return Status
     */
    public function setIssueId(int $issue_id): self
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCommitteeId(): ?int
    {
        return $this->committee_id;
    }

    /**
     * @param int $committee_id
     * @return Status
     */
    public function setCommitteeId(?int $committee_id): self
    {
        $this->committee_id = $committee_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpeechId(): ?string
    {
        return $this->speech_id;
    }

    /**
     * @param string $speech_id
     * @return Status
     */
    public function setSpeechId(?string $speech_id): self
    {
        $this->speech_id = $speech_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getDocumentId(): ?int
    {
        return $this->document_id;
    }

    /**
     * @param int $document_id
     * @return Status
     */
    public function setDocumentId(?int $document_id): self
    {
        $this->document_id = $document_id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Status
     */
    public function setDate(?DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Status
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Status
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommitteeName(): ?string
    {
        return $this->committee_name;
    }

    /**
     * @param string $committee_name
     * @return Status
     */
    public function setCommitteeName(?string $committee_name): self
    {
        $this->committee_name = $committee_name;
        return $this;
    }

    /**
     * @return int
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return Status
     */
    public function setValue(?int $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * @param boolean $complete
     * @return Status
     */
    public function setCompleted(bool $complete): self
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
