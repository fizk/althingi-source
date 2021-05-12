<?php

namespace Althingi\Model;

class CongressmanValue extends Congressman
{
    private ?int $value;

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            ['value' => $this->value]
        );
    }
}
