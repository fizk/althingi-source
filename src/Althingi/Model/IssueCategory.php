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

    public function toArray()
    {
        return [
            'category_id' => $this->category_id,
            'issue_id' => $this->issue_id,
            'assembly_id' => $this->assembly_id,
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
