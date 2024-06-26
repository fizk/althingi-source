<?php

namespace Althingi\Model;

use DateTime;

class DateAndCount implements ModelInterface
{
    private int $count = 0;
    private ?DateTime $date = null;

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count = 0): static
    {
        $this->count = $count;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date?->format('c'),
            'count' => $this->count
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
