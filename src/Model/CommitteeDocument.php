<?php

namespace Althingi\Model;

class CommitteeDocument implements ModelInterface
{
    /** @var int */
    private $document_committee_id = null;

    /** @var int */
    private $document_id;

    /** @var int */
    private $assembly_id;

    /** @var int */
    private $issue_id;

    /** @var string */
    private $category;

    /** @var int */
    private $committee_id;

    /** @var string */
    private $part = null;

    /** @var string */
    private $name = null;

    /**
     * @return int
     */
    public function getDocumentCommitteeId(): ? int
    {
        return $this->document_committee_id;
    }

    /**
     * @param int $document_committee_id
     * @return CommitteeDocument
     */
    public function setDocumentCommitteeId(? int $document_committee_id): self
    {
        $this->document_committee_id = $document_committee_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getDocumentId(): int
    {
        return $this->document_id;
    }

    /**
     * @param int $document_id
     * @return CommitteeDocument
     */
    public function setDocumentId(int $document_id): self
    {
        $this->document_id = $document_id;
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
     * @return CommitteeDocument
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
     * @return CommitteeDocument
     */
    public function setIssueId(int $issue_id): self
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return CommitteeDocument
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return int
     */
    public function getCommitteeId(): int
    {
        return $this->committee_id;
    }

    /**
     * @param int $committee_id
     * @return CommitteeDocument
     */
    public function setCommitteeId(int $committee_id): self
    {
        $this->committee_id = $committee_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPart(): ?string
    {
        return $this->part;
    }

    /**
     * @param string $part
     * @return CommitteeDocument
     */
    public function setPart(?string $part): self
    {
        $this->part = $part;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return CommitteeDocument
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
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
