<?php

namespace Althingi\Model;

class IssueCategory implements ModelInterface
{
    /** @var  int */
    private $category_id;

    /** @var  int */
    private $issue_id;

    /** @var  int */
    private $assembly_id;

    /** @var  string */
    private $category;

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    /**
     * @param int $category_id
     * @return IssueCategory
     */
    public function setCategoryId(int $category_id): IssueCategory
    {
        $this->category_id = $category_id;
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
     * @return IssueCategory
     */
    public function setIssueId(int $issue_id): IssueCategory
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
     * @return IssueCategory
     */
    public function setAssemblyId(int $assembly_id): IssueCategory
    {
        $this->assembly_id = $assembly_id;
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
     * @return IssueCategory
     */
    public function setCategory(string $category)
    {
        $this->category = $category;
        return $this;
    }

    public function toArray()
    {
        return [
            'category_id' => $this->category_id,
            'issue_id' => $this->issue_id,
            'assembly_id' => $this->assembly_id,
            'category' => $this->category,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
