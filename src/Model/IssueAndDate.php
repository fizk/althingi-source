<?php

namespace Althingi\Model;

use DateTime;

class IssueAndDate extends Issue
{
    private ?DateTime $date = null;

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'date' => $this->date?->format('Y-m-d H:i:s'),
        ]);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
