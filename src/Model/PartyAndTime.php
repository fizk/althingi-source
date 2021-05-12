<?php

namespace Althingi\Model;

class PartyAndTime extends Party
{
    private int $total_time = 0;

    public function getTotalTime(): int
    {
        return $this->total_time;
    }

    public function setTotalTime(int $total_time = 0): self
    {
        $this->total_time = $total_time;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            ['total_time' => $this->total_time]
        );
    }
}
