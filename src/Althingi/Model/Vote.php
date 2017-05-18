<?php

namespace Althingi\Model;

class Vote implements ModelInterface
{
    /** @var int */
    private $vote_id;

    /** @var int */
    private $issue_id;

    /** @var int */
    private $assembly_id;

    /** @var int */
    private $document_id;

    /** @var \DateTime */
    private $date;

    /** @var string */
    private $type;

    /** @var string */
    private $outcome;

    /** @var string */
    private $method;

    /** @var int */
    private $yes;

    /** @var int */
    private $no;

    /** @var int */
    private $inaction;

    /** @var string */
    private $committee_to;

    /**
     * @return int
     */
    public function getVoteId(): int
    {
        return $this->vote_id;
    }

    /**
     * @param int $vote_id
     * @return Vote
     */
    public function setVoteId(int $vote_id): Vote
    {
        $this->vote_id = $vote_id;
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
     * @return Vote
     */
    public function setIssueId(int $issue_id): Vote
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    /**
     * @param int $assembly_id
     * @return Vote
     */
    public function setAssemblyId(int $assembly_id): Vote
    {
        $this->assembly_id = $assembly_id;
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
     * @return Vote
     */
    public function setDocumentId(int $document_id = null): Vote
    {
        $this->document_id = $document_id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Vote
     */
    public function setDate(\DateTime $date = null): Vote
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Vote
     */
    public function setType(string $type = null): Vote
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutcome(): ?string
    {
        return $this->outcome;
    }

    /**
     * @param string $outcome
     * @return Vote
     */
    public function setOutcome(string $outcome = null): Vote
    {
        $this->outcome = $outcome;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return Vote
     */
    public function setMethod(string $method = null): Vote
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return int
     */
    public function getYes(): ?int
    {
        return $this->yes;
    }

    /**
     * @param int $yes
     * @return Vote
     */
    public function setYes(int $yes = null): Vote
    {
        $this->yes = $yes;
        return $this;
    }

    /**
     * @return int
     */
    public function getNo(): ?int
    {
        return $this->no;
    }

    /**
     * @param int $no
     * @return Vote
     */
    public function setNo(int $no = null): Vote
    {
        $this->no = $no;
        return $this;
    }

    /**
     * @return int
     */
    public function getInaction(): ?int
    {
        return $this->inaction;
    }

    /**
     * @param int $inaction
     * @return Vote
     */
    public function setInaction(int $inaction = null): Vote
    {
        $this->inaction = $inaction;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommitteeTo(): ?string
    {
        return $this->committee_to;
    }

    /**
     * @param string $committee_to
     * @return Vote
     */
    public function setCommitteeTo(string $committee_to = null): Vote
    {
        $this->committee_to = $committee_to;
        return $this;
    }

    public function toArray()
    {
        return [
            'vote_id' => $this->vote_id,
            'issue_id' => $this->issue_id,
            'assembly_id' => $this->assembly_id,
            'document_id' => $this->document_id,
            'date' => $this->date ? $this->date->format('Y-m-d H:m:s') : null,
            'type' => $this->type,
            'outcome' => $this->outcome,
            'method' => $this->method,
            'yes' => $this->yes,
            'no' => $this->no,
            'inaction' => $this->inaction,
            'committee_to' => $this->committee_to,
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
