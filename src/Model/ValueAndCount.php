<?php

namespace Althingi\Model;

class ValueAndCount implements ModelInterface
{
    private $count = 0;
    private ?string $value = null;

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'count' => $this->count
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
