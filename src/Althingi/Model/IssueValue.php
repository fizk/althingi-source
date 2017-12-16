<?php

namespace Althingi\Model;

class IssueValue extends Issue
{
    /** @var  int */
    private $value;

    /**
     * @return int
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return IssueValue
     */
    public function setValue(?int $value): IssueValue
    {
        $this->value = $value;
        return $this;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'value' => $this->value,
        ]);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
