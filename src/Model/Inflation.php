<?php

namespace Althingi\Model;

use DateTime;

class Inflation implements ModelInterface
{
    private int $id;
    private float $value;
    private DateTime $date;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'date' => $this->date?->format('Y-m-d'),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
