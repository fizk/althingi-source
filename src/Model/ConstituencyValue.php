<?php

namespace Althingi\Model;

class ConstituencyValue extends Constituency
{
    private int $value;

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): static
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
}
