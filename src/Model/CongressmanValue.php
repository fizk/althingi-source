<?php

namespace Althingi\Model;

class CongressmanValue extends Congressman
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
     * @return $this
     */
    public function setValue(?int $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            ['value' => $this->value]
        );
    }
}
