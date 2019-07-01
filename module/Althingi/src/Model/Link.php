<?php

namespace Althingi\Model;

use DateTime;

class Link implements ModelInterface
{
    /** @var  int */
    private $assembly_id;

    /** @var  int */
    private $issue_id;

    /** @var  string */
    private $category;

    /** @var  string */
    private $type;

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    /**
     * @param int $assembly_id
     * @return Link
     */
    public function setAssemblyId(int $assembly_id): Link
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
     * @return Link
     */
    public function setIssueId(int $issue_id): Link
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
     * @return Link
     */
    public function setCategory(string $category): Link
    {
        $this->category = $category;
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
     * @return Link
     */
    public function setType(string $type): Link
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'assembly_id' => $this->assembly_id,
            'issue_id' => $this->issue_id,
            'category' => $this->category,
            'type' => $this->type,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
