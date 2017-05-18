<?php

namespace Althingi\Model;

class VoteItemAndAssemblyIssue extends VoteItem
{
    /** @var int */
    private $issue_id;

    /** @var int */
    private $assembly_id;

    /**
     * @return int
     */
    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    /**
     * @param int $issue_id
     * @return VoteItemAndAssemblyIssue
     */
    public function setIssueId(int $issue_id): VoteItemAndAssemblyIssue
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
     * @return VoteItemAndAssemblyIssue
     */
    public function setAssemblyId(int $assembly_id): VoteItemAndAssemblyIssue
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            ['issue_id' => $this->issue_id, 'assembly_id' => $this->assembly_id]
        );
    }
}
