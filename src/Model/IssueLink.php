<?php

namespace Althingi\Model;

class IssueLink implements ModelInterface
{
    /** @var int */
    private $from_assembly_id;

    /** @var int */
    private $from_issue_id;

    /** @var string */
    private $from_category;

    /** @var int */
    private $assembly_id;

    /** @var int */
    private $issue_id;

    /** @var string */
    private $category;

    /** @var string */
    private $type;

    /**
     * @return int
     */
    public function getFromAssemblyId(): int
    {
        return $this->from_assembly_id;
    }

    /**
     * @param int $from_assembly_id
     * @return IssueLink
     */
    public function setFromAssemblyId(int $from_assembly_id): IssueLink
    {
        $this->from_assembly_id = $from_assembly_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getFromIssueId(): int
    {
        return $this->from_issue_id;
    }

    /**
     * @param int $from_issue_id
     * @return IssueLink
     */
    public function setFromIssueId(int $from_issue_id): IssueLink
    {
        $this->from_issue_id = $from_issue_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromCategory(): string
    {
        return $this->from_category;
    }

    /**
     * @param string $from_category
     * @return IssueLink
     */
    public function setFromCategory(string $from_category): IssueLink
    {
        $this->from_category = $from_category;
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
     * @return IssueLink
     */
    public function setAssemblyId(int $assembly_id): IssueLink
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
     * @return IssueLink
     */
    public function setIssueId(int $issue_id): IssueLink
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
     * @return IssueLink
     */
    public function setCategory(string $category): IssueLink
    {
        $this->category = $category;
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
     * @return IssueLink
     */
    public function setType(?string $type): IssueLink
    {
        $this->type = $type;
        return $this;
    }

    public function toArray()
    {
        return [
            'to_assembly_id' => $this->assembly_id,
            'to_issue_id' => $this->issue_id,
            'to_category' => $this->category,
            'from_assembly_id' => $this->from_assembly_id,
            'from_issue_id' => $this->from_issue_id,
            'from_category' => $this->from_category,
            'type' => $this->type,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
