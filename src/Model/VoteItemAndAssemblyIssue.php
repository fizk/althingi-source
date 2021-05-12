<?php

namespace Althingi\Model;

class VoteItemAndAssemblyIssue extends VoteItem
{
    private int $issue_id;
    private int $assembly_id;

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

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            ['issue_id' => $this->issue_id, 'assembly_id' => $this->assembly_id]
        );
    }
}
