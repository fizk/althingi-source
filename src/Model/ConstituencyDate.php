<?php

namespace Althingi\Model;

use DateTime;

class ConstituencyDate extends Constituency
{
    private ?DateTime $date = null;

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
        return array_merge(parent::toArray(), [
            'date' => $this->date?->format('Y-m-d'),
        ]);
    }
}
