<?php

namespace Althingi\Model;

class IssueTypeStatus implements ModelInterface
{
    /** @var  int */
    private $count;

    /** @var  string */
    private $status;

    /**
     * @return int
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return IssueTypeStatus
     */
    public function setCount(int $count = null): IssueTypeStatus
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return IssueTypeStatus
     */
    public function setStatus(string $status = null): IssueTypeStatus
    {
        $this->status = $status;
        return $this;
    }

    public function toArray()
    {
        return [
            'count' => $this->count,
            'status' => $this->status,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
