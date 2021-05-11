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
    public function setValue(?int $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'value' => $this->value,
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
